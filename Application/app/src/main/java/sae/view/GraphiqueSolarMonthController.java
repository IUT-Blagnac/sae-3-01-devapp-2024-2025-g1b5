package sae.view;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.XYChart;
import javafx.stage.Stage;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.NumberAxis;
import javafx.event.ActionEvent;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import java.io.File;
import java.io.FileReader;
import java.util.HashMap;
import java.util.Map;

public class GraphiqueSolarMonthController {

    @FXML
    private BarChart<String, Number> barChart;

    @FXML
    private CategoryAxis xAxis;

    @FXML
    private NumberAxis yAxis;

    private JSONObject solarData;

    private Stage graphiqueStage;

    public void setDatas(Stage stage) {
        this.graphiqueStage = stage;
    }

    @FXML
    public void initialize() {
        // Charger les données depuis le fichier JSON
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
     * Affiche le graphique avec les données d'énergie produite par mois.
     */
    private void afficherGraphique() {
        if (solarData == null) {
            System.out.println("Aucune donnée à afficher !");
            return;
        }
        
        // Créer une carte pour stocker l'énergie produite par mois
        Map<String, Double> energyByMonth = new HashMap<>();

        // Parcourir les données pour remplir les barres du graphique
        for (Object key : solarData.keySet()) {
            JSONObject entry = (JSONObject) solarData.get(key);

            // Extraire la date de la mise à jour
            String lastUpdateTime = (String) entry.get("lastUpdateTime");
            String yearMonth = lastUpdateTime.substring(0, 7); // "YYYY-MM"
            
            // Vérifier si 'lastDayData' est présent et contient la clé 'energy'
            JSONObject lastDayData = (JSONObject) entry.get("lastDayData");
            if (lastDayData != null && lastDayData.containsKey("energy")) {
                // Utilisation de Number pour accepter Long et Double
                Number energyProduced = (Number) lastDayData.get("energy");  // Energie produite le dernier jour

                if (energyProduced == null) {
                    energyProduced = 0.0;  // Valeur par défaut si l'énergie est nulle
                }

                // Additionner l'énergie produite pour chaque mois
                energyByMonth.put(yearMonth, energyByMonth.getOrDefault(yearMonth, 0.0) + energyProduced.doubleValue());
            } else {
                System.out.println("Données 'lastDayData' ou 'energy' manquantes pour la date: " + yearMonth);
            }
        }

        // Créer une série de données pour le graphique
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Energie produite (Wh)");

        // Ajouter les données de l'énergie produite par mois à la série
        for (Map.Entry<String, Double> entry : energyByMonth.entrySet()) {
            series.getData().add(new XYChart.Data<>(entry.getKey(), entry.getValue()));
        }

        // Ajouter la série au graphique
        barChart.getData().clear();
        barChart.getData().add(series);
    }

    @FXML
    private void actionRetour(ActionEvent event) {
        Stage stage = (Stage) barChart.getScene().getWindow(); 
        stage.close();  
    }
}
