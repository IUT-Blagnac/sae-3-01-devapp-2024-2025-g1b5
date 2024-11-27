package sae.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;
import org.eclipse.paho.client.mqttv3.MqttClient;
import org.eclipse.paho.client.mqttv3.MqttConnectOptions;
import org.eclipse.paho.client.mqttv3.MqttException;
import org.eclipse.paho.client.mqttv3.persist.MemoryPersistence;

public class MenuController  {
    
    @SuppressWarnings("unused")
    private Stage fenetrePrincipale;

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
    

    public void setDatas(Stage fenetre,  App app) {
		this.application = app;
		this.fenetrePrincipale = fenetre;
		//this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
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
            System.out.println("Connexion réussie");
          }
      } catch (MqttException e) {
        // TODO Auto-generated catch block
        e.printStackTrace();
      }
    }

    @FXML
    private void actionBouttonConfig() {
    application.loadMenuConfig();
  }
   
}
