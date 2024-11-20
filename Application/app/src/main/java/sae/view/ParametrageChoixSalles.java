package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;

public class ParametrageChoixSalles {
    
    private Stage fenetrePrincipale ;

    @FXML
    private Button boutton ;
    @FXML
    private Button butRetour ;

    private App application;


    public void setDatas(Stage fenetrePrincipale,  App app) {
		this.application = app;
		this.fenetrePrincipale = fenetrePrincipale;
		//this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	}

    @FXML
    private void actionRetour() {
		application.loadMenu();
	}

}
