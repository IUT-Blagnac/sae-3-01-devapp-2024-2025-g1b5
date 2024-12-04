package sae.view;

import java.util.HashMap;
import java.util.Map;

import javafx.fxml.FXML;
import javafx.scene.Scene;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Alert.AlertType;
import javafx.stage.Stage;
import sae.App;

public class GrapheSalleController {
    
    private Stage fenetrePrincipale;
    private App application;

    @FXML
    private BarChart graphe;

    @FXML
    private CategoryAxis xAxe;

    @FXML
    private NumberAxis yAxe;

    @FXML
    private Button retour;

    @FXML
    private LineChart lineChart;

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }

    public void populateHistogram(String salle, Map<String, Object> dico) {
        // Nettoyer le graphique avant d'ajouter de nouvelles données
        graphe.getData().clear();
        xAxe.getCategories().clear();
    
        // Créer une série pour le graphique
        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName("Données pour " + salle);
    
        // Ajouter les données depuis le dictionnaire
        for (Map.Entry<String, Object> entry : dico.entrySet()) {
            String key = entry.getKey(); // Nom de la catégorie (par exemple, "Température")
            Double value;
            if( entry.getValue() instanceof Double){
                value=(Double)entry.getValue();
            }
            else if(entry.getValue() instanceof Long){
                value=Double.parseDouble(Long.toString((Long)entry.getValue()));
            } else{
                Alert alert=new Alert(AlertType.ERROR, "Erreur Critique l'application trouve des valeurs innatendues!");
                alert.show();
                value=Double.valueOf(0);
            }

    
            // Ajouter la catégorie à l'axe X
            xAxe.getCategories().add(key);
    
            // Ajouter la donnée dans la série
            series.getData().add(new XYChart.Data<>(key, value));
        }
    
        // Ajouter la série au graphique
        graphe.getData().add(series);
    }
    
    @FXML
    private void actionRetour() {
        application.loadParametrageSalles();
    }

    public void lignePts(String salle, Map<String, Map<String, Object>> dico) {
        lineChart.getData().clear();
        
        final CategoryAxis xAxis = new CategoryAxis();
        final NumberAxis yAxis = new NumberAxis();
        lineChart.setTitle("Historique de : " + salle);
        
        // Un map pour stocker les séries par type de donnée (par exemple temperature, humidity, etc.)
        Map<String, XYChart.Series> seriesMap = new HashMap<>();
        
        for (Map.Entry<String, Map<String, Object>> entry : dico.entrySet()) {
            String mois = entry.getKey(); 
            Map<String, Object> data = entry.getValue(); // La valeur contient les données pour chaque type (par ex. temperature, humidity)

            // Parcours des sous-dictionnaires de chaque mois (par exemple, "temperature", "humidity")
            for (Map.Entry<String, Object> dataEntry : data.entrySet()) {
                String category = dataEntry.getKey(); // La clé dans ce sous-dictionnaire (par exemple "temperature", "humidity")
                Double value = getConvertedValue(dataEntry.getValue()); 
                
                // Si la série pour cette catégorie n'existe pas encore, on la crée
                if (!seriesMap.containsKey(category)) {
                    XYChart.Series series = new XYChart.Series();
                    series.setName(category); // Le nom de la série sera le nom de la catégorie (par exemple "temperature")
                    seriesMap.put(category, series); 
                }

                // Ajouter la donnée à la série appropriée
                seriesMap.get(category).getData().add(new XYChart.Data(mois, value));
            }
        }

        // Ajoute toutes les séries au graphique
        lineChart.getData().addAll(seriesMap.values());
    }

    // Méthode pour convertir les valeurs en Double
    private Double getConvertedValue(Object value) {
        if (value instanceof Double) {
            return (Double) value;
        } else if (value instanceof Long) {
            return ((Long) value).doubleValue();
        } else {
            // Gestion d'erreur si la valeur n'est ni un Long ni un Double
            Alert alert=new Alert(AlertType.ERROR, "Erreur Critique l'application trouve des valeurs innatendues!");
            alert.show();
            return 0.0;  // Retourne une valeur par défaut en cas d'erreur
        }
    }

    

}
