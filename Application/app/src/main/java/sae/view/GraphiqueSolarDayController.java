package sae.view;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.XYChart;
import javafx.stage.Stage;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.NumberAxis;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import java.io.File;
import java.io.FileReader;

public class GraphiqueSolarDayController {

    @FXML
    private BarChart<String, Number> barChart;

    @FXML
    private CategoryAxis xAxis;

    @FXML
    private NumberAxis yAxis;

    private JSONObject solarData;

    private Stage graphiqueStage;  // Par exemple, pour manipuler la fenêtre (Stage)

    // Ajoutez une méthode pour recevoir les données
    public void setDatas(Stage stage) {
        this.graphiqueStage = stage;

        // Vous pouvez ajouter d'autres initialisations ou traitements des données ici
        // Par exemple, vous pouvez charger des données de graphique, configurer des éléments de la vue, etc.
    }

    /**
     * Méthode d'initialisation pour charger les données et afficher le graphique.
     */
    @FXML
    public void initialize() {
        // Charger les données depuis le fichier
        chargerFichierSolar();
        
        // Vérifier si les données sont chargées
        if (solarData != null) {
            // Afficher les données sur le graphique
            afficherGraphique();
        } else {
            System.out.println("Aucune donnée à afficher !");
        }
    }

    /**
     * Charge le fichier JSON contenant les données de l'énergie produite.
     */
    private void chargerFichierSolar() {
        JSONParser parser = new JSONParser();
        
        try {
            File file = new File("Iot/solar.json"); // Chemin du fichier JSON

            if (!file.exists()) {
                System.out.println("Le fichier solar.json est introuvable.");
                return;
            }

            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            this.solarData = (JSONObject) json.get("solar");
            
            reader.close();
        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
            e.printStackTrace();
        }
    }

    /**
     * Affiche le graphique avec les données d'énergie produite par jour.
     */
    private void afficherGraphique() {
        if (solarData == null) {
            System.out.println("Aucune donnée à afficher !");
            return;
        }
    
        // Créer une série de données pour le graphique
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Energie produite (Wh)");
    
        // Parcourir les données pour remplir les barres
        for (Object key : solarData.keySet()) {
            JSONObject entry = (JSONObject) solarData.get(key);
    
            // Extraire la date de la mise à jour
            String lastUpdateTime = (String) entry.get("lastUpdateTime");
            String date = lastUpdateTime.split(" ")[0]; // "YYYY-MM-DD"
            System.out.println("Traitement de la date: " + date); // Afficher la date pour vérifier
    
            // Vérifier si 'lastDayData' est présent et contient la clé 'energy'
            JSONObject lastDayData = (JSONObject) entry.get("lastDayData");
            if (lastDayData != null && lastDayData.containsKey("energy")) {
                // Utilisation de Number pour accepter Long et Double
                Number energyProduced = (Number) lastDayData.get("energy");  // Energie produite le dernier jour
                System.out.println("Energie produite le " + date + ": " + energyProduced); // Afficher l'énergie pour déboguer
    
                // Si energyProduced est null ou invalide, on continue
                if (energyProduced == null) {
                    energyProduced = 0.0;  // Valeur par défaut si l'énergie est nulle
                }
    
                // Ajouter la donnée à la série
                series.getData().add(new XYChart.Data<>(date, energyProduced.doubleValue()));  // Convertir en double
            } else {
                System.out.println("Données 'lastDayData' ou 'energy' manquantes pour la date: " + date);
            }
        }
    
        // Ajouter la série au graphique
        barChart.getData().clear();
        barChart.getData().add(series);
    }
    
    
}
