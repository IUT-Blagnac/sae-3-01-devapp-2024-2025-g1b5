package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;
import sae.view.AppState;

import java.io.IOException;

public class MenuController {

    private Stage fenetrePrincipale;
    private Process pythonProcess;

    @FXML
    private Button butSalles;
    @FXML
    private Button butSolar;
    @FXML
    private Button butAlarmes;
    @FXML
    private Button butTestCo;
    @FXML
    private Button butConfig;

    private App application;

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }

    @FXML
    private void actionBouttonSalles() {
        application.loadParametrageSalles();
    }

    @FXML
    private void actionBouttonSolar() {
        application.loadParametrageSolar();
    }

    @FXML
    private void actionBouttonConnexion() {
        // Test de connexion MQTT
    }

    @FXML
    private void actionBouttonConfig() {
    application.loadMenuConfig();
  }

  @FXML
  private void actionBouttonAlarmes() {
      application.loadAlarmes();
  }


}
