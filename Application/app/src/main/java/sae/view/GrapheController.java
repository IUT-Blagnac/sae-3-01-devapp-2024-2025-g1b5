package sae.view;

import java.util.Map;

import javafx.fxml.FXML;
import javafx.scene.chart.BarChart;
import javafx.scene.chart.CategoryAxis;
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

}
