package sae.controller;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.input.MouseEvent;

public class MonController {

    @FXML
    private Button btnCapteursPanneauxSolaire;

    @FXML
    private Button btnCapteursSalles;

    @FXML
    private Button btnTestConnexionMqtt;

    @FXML
    private void handleCapteursPanneauxSolaire(MouseEvent event) {
        System.out.println("Vous avez cliqué sur Capteurs panneaux solaire");
    }

    @FXML
    private void handleCapteursSalles(MouseEvent event) {
        System.out.println("Vous avez cliqué sur Capteurs salles");
    }

    @FXML
    private void handleTestConnexionMqtt(MouseEvent event) {
        System.out.println("Vous avez cliqué sur Test connexion MQTT");
    }
}
