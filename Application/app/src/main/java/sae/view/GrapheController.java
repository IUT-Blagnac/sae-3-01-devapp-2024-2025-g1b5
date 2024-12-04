package sae.view;

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

public class GrapheController {
    
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

    public void lignePts(String salle, Map<String, Object> dico) {
       
        lineChart.getData().clear();

        final CategoryAxis xAxis = new CategoryAxis();
        final NumberAxis yAxis = new NumberAxis();
        //xAxis.setLabel("Month");
       
        lineChart.setTitle("Historique de : " + salle);
                          
        XYChart.Series series1 = new XYChart.Series();
        series1.setName("Portfolio 1");
        
        series1.getData().add(new XYChart.Data("Jan", 23));
        series1.getData().add(new XYChart.Data("Feb", 14));
        series1.getData().add(new XYChart.Data("Mar", 15));
        series1.getData().add(new XYChart.Data("Apr", 24));
        series1.getData().add(new XYChart.Data("May", 34));
        series1.getData().add(new XYChart.Data("Jun", 36));
        series1.getData().add(new XYChart.Data("Jul", 22));
        series1.getData().add(new XYChart.Data("Aug", 45));
        series1.getData().add(new XYChart.Data("Sep", 43));
        series1.getData().add(new XYChart.Data("Oct", 17));
        series1.getData().add(new XYChart.Data("Nov", 29));
        series1.getData().add(new XYChart.Data("Dec", 25));
        
        XYChart.Series series2 = new XYChart.Series();
        series2.setName("Portfolio 2");
        series2.getData().add(new XYChart.Data("Jan", 33));
        series2.getData().add(new XYChart.Data("Feb", 34));
        series2.getData().add(new XYChart.Data("Mar", 25));
        series2.getData().add(new XYChart.Data("Apr", 44));
        series2.getData().add(new XYChart.Data("May", 39));
        series2.getData().add(new XYChart.Data("Jun", 16));
        series2.getData().add(new XYChart.Data("Jul", 55));
        series2.getData().add(new XYChart.Data("Aug", 54));
        series2.getData().add(new XYChart.Data("Sep", 48));
        series2.getData().add(new XYChart.Data("Oct", 27));
        series2.getData().add(new XYChart.Data("Nov", 37));
        series2.getData().add(new XYChart.Data("Dec", 29));
        
        XYChart.Series series3 = new XYChart.Series();
        series3.setName("Portfolio 3");
        series3.getData().add(new XYChart.Data("Jan", 44));
        series3.getData().add(new XYChart.Data("Feb", 35));
        series3.getData().add(new XYChart.Data("Mar", 36));
        series3.getData().add(new XYChart.Data("Apr", 33));
        series3.getData().add(new XYChart.Data("May", 31));
        series3.getData().add(new XYChart.Data("Jun", 26));
        series3.getData().add(new XYChart.Data("Jul", 22));
        series3.getData().add(new XYChart.Data("Aug", 25));
        series3.getData().add(new XYChart.Data("Sep", 43));
        series3.getData().add(new XYChart.Data("Oct", 44));
        series3.getData().add(new XYChart.Data("Nov", 45));
        series3.getData().add(new XYChart.Data("Dec", 44));
     
        lineChart.getData().addAll(series1, series2, series3);
  
    }

}
