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
    
    private Stage fenetrePrincipale;

    @FXML
    private Button butSalles;
    @FXML
    private Button butSolar;
    @FXML
    private Button butAlarmes;
    @FXML
    private Button butTestCo;
    
    private App application;
    

    public void setDatas(Stage fenetrePrincipale,  App app) {
		this.application = app;
		this.fenetrePrincipale = fenetrePrincipale;
		//this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	}

    @FXML
    private void actionBouttonSalles() {
		application.loadParametrageSalles();
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
   
}
