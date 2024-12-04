package sae.view;

import java.util.Map;

import javafx.fxml.FXML;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import sae.App;

public class EvolutionGrapheController {

    private Stage fenetrePrincipale;
    private App application;

    @FXML
    private GridPane gridDynamique;
    @FXML
    private Button retour;

    @FXML
    private Label titreSalle;
    
    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
        this.fenetrePrincipale.setMaximized(true);
    }

    @FXML
    private void actionRetour() {
        application.loadParametrageSalles();
    }

    public void afficherGraphes(String salle, Map<String, Map<String, Object>> dico){

    }
    

}