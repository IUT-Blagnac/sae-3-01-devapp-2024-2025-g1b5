package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.Labeled;
import javafx.stage.Stage;
import sae.App;
import sae.appli.AppState;

import java.io.IOException;

import org.eclipse.paho.client.mqttv3.MqttClient;
import org.eclipse.paho.client.mqttv3.MqttConnectOptions;
import org.eclipse.paho.client.mqttv3.MqttException;
import org.eclipse.paho.client.mqttv3.persist.MemoryPersistence;

public class MenuController {

    @SuppressWarnings("unused")
    private Stage fenetrePrincipale;
    @SuppressWarnings("unused")
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
    @FXML
    Label labelTestCo;

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
        try {
        MqttClient client = new MqttClient(
          "tcp://mqtt.iut-blagnac.fr:1883",
          MqttClient.generateClientId(),
          new MemoryPersistence());
          MqttConnectOptions options = new MqttConnectOptions();
          client.connect(options);
          if(client.isConnected()){
            
            labelTestCo.setText("Connexion réussie");
            System.out.println("Connexion réussie");
            
          } else {
            labelTestCo.setText("Connexion échouée");
            labelTestCo.setStyle("-fx-text-fill: green;");
            System.out.println("Connexion échouée");
          }
      } catch (MqttException e) {
        // TODO Auto-generated catch block
        e.printStackTrace();
        labelTestCo.setText("Connexion échouée");
        labelTestCo.setStyle("-fx-text-fill: red;");
      }
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
