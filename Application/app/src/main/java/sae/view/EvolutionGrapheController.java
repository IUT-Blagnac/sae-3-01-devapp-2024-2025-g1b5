package sae.view;

import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import javafx.fxml.FXML;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.FlowPane;
import javafx.stage.Stage;
import sae.App;

public class EvolutionGrapheController {

    private Stage fenetrePrincipale;
    private App application;

    @FXML
    private FlowPane gridDynamique;
    @FXML
    private Button retour;

    @FXML
    private Label titreSalle;
    
    public void setDatas(Stage fenetre, App app, BorderPane vueListe) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
        this.fenetrePrincipale.setMaximized(true);
               
    }

    @FXML
    private void actionRetour() {
        this.fenetrePrincipale.setMaximized(false);
        application.loadParametrageSalles();
    }

    

    public void afficherGraphes(String salle, Map<String, Map<String, Object>> dico) {
        this.titreSalle.setText("Historique de : " + salle);
        // Index pour positionnement dans le GridPane
        int row = 0;
        int col = 0;
    
        // Parcours des catégories
        Set<String> categories = new HashSet<>();
        for (Map<String, Object> data : dico.values()) {
            categories.addAll(data.keySet()); // Récupérer toutes les catégories uniques
        }

        
        

        // Création des graphiques pour chaque catégorie
        for (String category : categories) {
            // Créer un LineChart pour chaque catégorie
            final CategoryAxis xAxis = new CategoryAxis();
            final NumberAxis yAxis = new NumberAxis();
            LineChart<String, Number> lineChart = new LineChart<>(xAxis, yAxis);
            lineChart.setTitle(category.toUpperCase());


            // Créer une série pour la catégorie
            XYChart.Series<String, Number> series = new XYChart.Series<>();
            series.setName(category);

            // Remplir la série avec les données
            for (Map.Entry<String, Map<String, Object>> entry : dico.entrySet()) {
                String mois = entry.getKey();
                Map<String, Object> data = entry.getValue();

                // Ajouter la donnée à la série si elle existe
                if (data.containsKey(category)) {
                    Object value = data.get(category);
                    series.getData().add(new XYChart.Data<>(mois, getConvertedValue(value)));
                }
            }
    
            // Ajouter la série au graphique
            lineChart.getData().add(series);
            
            
            // Ajouter le graphique au GridPane
            gridDynamique.getChildren().add(lineChart);

            // Alterner entre colonnes et lignes
            if (col == 0) {
                col = 1; // Aller à la colonne suivante
            } else {
                col = 0; // Revenir à la première colonne
                row++;   // Passer à la ligne suivante
            }
        }
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