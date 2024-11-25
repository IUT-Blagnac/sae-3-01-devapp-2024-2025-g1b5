package sae.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;
import org.eclipse.paho.client.mqttv3.MqttClient;
import org.eclipse.paho.client.mqttv3.MqttConnectOptions;
import org.eclipse.paho.client.mqttv3.MqttException;

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

  if(butTestCo.isPressed()){
    // Code to test the connection

  }

    
   
}
