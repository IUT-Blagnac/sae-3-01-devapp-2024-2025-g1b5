package sae.view;

import javafx.fxml.FXML;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;
import javafx.scene.chart.NumberAxis;
import javafx.stage.Stage;
import sae.App;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import java.io.File;
import java.io.FileReader;
import java.util.ArrayList;
import java.util.List;

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
    
        List<Double> puissances = new ArrayList<>();
        List<Integer> heures = new ArrayList<>();
    
        // Parcourir les clés de "solar" (0, 1, 2, ...)
        for (Object key : solarData.keySet()) {
            JSONObject entry = (JSONObject) solarData.get(key);
    
            // Récupérer "lastUpdateTime"
            String lastUpdateTime = (String) entry.get("lastUpdateTime");
            String[] timeParts = lastUpdateTime.split(" ");
            String time = timeParts[1]; // Partie HH:mm:ss
    
            // Extraire l'heure (HH)
            String[] timeElements = time.split(":");
            int hour = Integer.parseInt(timeElements[0]);
    
            // Récupérer "currentPower" -> "power"
            JSONObject currentPower = (JSONObject) entry.get("currentPower");
            Double power = currentPower != null ? ((Number) currentPower.get("power")).doubleValue() : 0.0;
    
            heures.add(hour);
            puissances.add(power);
        }
    
        // Créer et ajouter une série au graphique
        XYChart.Series<Number, Number> series = new XYChart.Series<>();
        for (int i = 0; i < heures.size(); i++) {
            series.getData().add(new XYChart.Data<>(heures.get(i), puissances.get(i)));
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
            File file = new File("Iot/solar.json");
    
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
