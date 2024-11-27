package sae.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;


public class ConfigController  {
    
    private Stage fenetrePrincipale;

    @FXML
    private Button butConfigSalles;
    @FXML
    private Button butConfigSolar;
    @FXML
    private Button butRetour;
    
    private App application;
    

    public void setDatas(Stage fenetre,  App app) {
		this.application = app;
		this.fenetrePrincipale = fenetre;
		//this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	}


    @FXML
    private void actionRetour() {
        application.loadMenu();
    }

    @FXML
    private void actionSallesConfig() {
        application.loadMenu();
    }

    @FXML
    private void actionSolarConfig() {
        application.loadSolarConfig();
    }
   
}
