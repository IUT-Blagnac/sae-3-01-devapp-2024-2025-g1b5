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

        // Index pour positionnement dans le FlowPane dynamique
        int row = 0;
        int col = 0;
    
        // Parcours des catégories de données
        Set<String> categories = new HashSet<>();
        for (Map<String, Object> data : dico.values()) {
            categories.addAll(data.keySet()); // Récupérer toutes les catégories uniques
        }

        // Création des graphiques pour chaque catégorie
        for (String cat : categories) {
            final CategoryAxis xAxis = new CategoryAxis();
            final NumberAxis yAxis = new NumberAxis();

            LineChart<String, Number> lineChart = new LineChart<>(xAxis, yAxis);
            lineChart.setTitle(cat.toUpperCase()); //titre graphique

            // Taille maximale graphique pour le visuel
            if ( categories.size() < 4) {
                lineChart.setMaxWidth(600);  // Largeur max
                lineChart.setMaxHeight(400); // Hauteur max
            } else if ( categories.size() < 7 ) {
                lineChart.setMaxWidth(500);  // Largeur max
                lineChart.setMaxHeight(300); // Hauteur max
            } else  {
                lineChart.setMaxWidth(400);  // Largeur max
                lineChart.setMaxHeight(200); // Hauteur max
            }

            // Créer une série / ligne de points 
            XYChart.Series<String, Number> series = new XYChart.Series<>();
            lineChart.setLegendVisible(false); // desactivation sur de légende pour le graphique


            // établir la série de pts avec les données
            for (Map.Entry<String, Map<String, Object>> entry : dico.entrySet()) {
                String temps = entry.getKey();
                Map<String, Object> data = entry.getValue();

                // Ajouter la donnée à la série si elle existe
                if (data.containsKey(cat)) {
                    Object value = data.get(cat);
                    series.getData().add(new XYChart.Data<>(temps, getConvertedValue(value)));
                }
            }
    
            // Ajouter la série au graphique
            lineChart.getData().add(series);
            
            // Ajouter le graphique au FlowPane
            gridDynamique.getChildren().add(lineChart);

            // Alterner entre colonnes et lignes
            if (col == 0) {
                col = 1; // colonne suivante
            } else {
                col = 0; //revient 1ere col
                row++;   //ligne suivante
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