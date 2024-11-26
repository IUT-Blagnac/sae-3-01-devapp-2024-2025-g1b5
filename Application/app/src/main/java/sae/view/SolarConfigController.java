package sae.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;


public class SolarConfigController  {
    
    private Stage fenetrePrincipale;
    
    private App application;

    @FXML
    Button butRetour ;
    

    public void setDatas(Stage fenetre,  App app) {
		this.application = app;
		this.fenetrePrincipale = fenetre;
	}

    @FXML
    private void actionRetour() {
        application.loadMenuConfig();
    }
}