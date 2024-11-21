package sae.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;

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


    public void setDatas(Stage fenetre,  App app) {
		this.application = app;
		this.fenetrePrincipale = fenetre;
		//this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	}


    @FXML
    private void actionBouttonSalles() {
		application.loadParametrageSalles();
	}

    
   
}
