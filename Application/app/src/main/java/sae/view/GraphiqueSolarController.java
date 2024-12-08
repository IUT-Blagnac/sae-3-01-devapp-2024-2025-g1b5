package sae.view;

import javafx.fxml.FXML;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;
import javafx.scene.chart.NumberAxis;
import javafx.stage.Stage;
import sae.App;
import sae.appli.DataPoint;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import java.io.File;
import java.io.FileReader;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Comparator;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

public class GraphiqueSolarController {

    @FXML
    private LineChart<Number, Number> lineChart;

    private App application;

    private Stage fenetrePrincipale;

    private JSONObject solarData;

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }

    @FXML
    public void initialize() {
        // Charger les données JSON et afficher le graphique
        chargerFichierSolar();
        afficherGraphique();
    }

    /**
     * Affiche le graphique de la puissance en fonction du temps
     */
    private void afficherGraphique() {
        if (solarData == null) {
            System.out.println("Aucune donnée chargée depuis le fichier JSON !");
            return;
        }
    
        List<DataPoint> dataPoints = new ArrayList<>();
        Set<String> uniqueEntries = new HashSet<>(); // Ensemble pour vérifier les doublons
    
        long premierTemps = Long.MAX_VALUE; // Pour trouver le premier timestamp absolu
    
        // Parcourir les clés de "solar" (0, 1, 2, ...)
        for (Object key : solarData.keySet()) {
            JSONObject entry = (JSONObject) solarData.get(key);
    
            // Récupérer "lastUpdateTime"
            String lastUpdateTime = (String) entry.get("lastUpdateTime");
    
            // Convertir en timestamp absolu (millisecondes depuis l'époque UNIX)
            long timestamp;
            try {
                timestamp = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").parse(lastUpdateTime).getTime();
            } catch (Exception e) {
                System.out.println("Erreur lors du parsing de la date : " + lastUpdateTime);
                continue;
            }
    
            // Récupérer "currentPower" -> "power"
            JSONObject currentPower = (JSONObject) entry.get("currentPower");
            Double power = currentPower != null ? ((Number) currentPower.get("power")).doubleValue() : 0.0;
    
            // Générer une clé unique en utilisant lastUpdateTime et power
            String uniqueKey = lastUpdateTime + ":" + power;
    
            // Si cette clé n'existe pas déjà, ajouter le point
            if (!uniqueEntries.contains(uniqueKey)) {
                uniqueEntries.add(uniqueKey);  // Ajouter la clé unique pour éviter les doublons
                DataPoint point = new DataPoint(timestamp, power);  // Créer un nouveau point
                dataPoints.add(point);  // Ajouter le point à la liste
                premierTemps = Math.min(premierTemps, timestamp); // Mettre à jour le premier temps
            }
        }
    
        if (dataPoints.isEmpty()) {
            System.out.println("Aucune donnée valide à afficher !");
            return;
        }
    
        // Tri des données par temps
        dataPoints.sort(Comparator.comparingLong(DataPoint::getTime));
    
        // Convertir les timestamps en temps relatif (en heures, avec les jours inclus)
        List<Double> tempsHeures = new ArrayList<>();
        List<Double> puissances = new ArrayList<>();
    
        for (DataPoint point : dataPoints) {
            double heuresDepuisDebut = (double) (point.getTime() - premierTemps) / (1000 * 60 * 60);
            tempsHeures.add(heuresDepuisDebut);
            puissances.add(point.getPower());
        }
    
        // Ajuster dynamiquement l'axe des abscisses
        NumberAxis xAxis = (NumberAxis) lineChart.getXAxis();
        xAxis.setLowerBound(0);
        xAxis.setUpperBound(tempsHeures.get(tempsHeures.size() - 1));
        xAxis.setTickUnit(Math.ceil((xAxis.getUpperBound() - xAxis.getLowerBound()) / 10));
    
        // Créer et ajouter une série au graphique
        XYChart.Series<Number, Number> series = new XYChart.Series<>();
        for (int i = 0; i < tempsHeures.size(); i++) {
            series.getData().add(new XYChart.Data<>(tempsHeures.get(i), puissances.get(i)));
        }
    
        lineChart.getData().clear();
        lineChart.getData().add(series);
    }
    

    /**
     * Charge le fichier JSON et stocke les données dans le champ solarData
     */
    public void chargerFichierSolar() {
        JSONParser parser = new JSONParser();

        try {
            // Charger le fichier JSON
            File file = new File("../../Iot/solar.json");

            if (!file.exists()) {
                System.out.println("Le fichier solar.json est introuvable à la racine du projet.");
                return;
            }

            // Lire et analyser le fichier JSON
            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            // Vérifier et extraire l'objet "solar"
            JSONObject solarJson = (JSONObject) json.get("solar");
            if (solarJson == null) {
                System.out.println("Clé 'solar' manquante dans le fichier JSON.");
                return;
            }

            // Stocker les données dans le champ solarData
            this.solarData = solarJson;

            reader.close();
        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
            e.printStackTrace();
        }
    }

    /**
     * Retourne à la fenêtre précédente
     */
    @FXML
    private void actionRetour() {
        Stage stage = (Stage) lineChart.getScene().getWindow();
        stage.close();
    }
}
