package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import sae.App;

public class AfficherDonneesController {

    private Stage fenetrePrincipale ;

    private App application;

    @FXML
    private Label titreSalle;

    
    public void setDatas(Stage fenetre,  App app) {
      this.application = app;
      this.fenetrePrincipale = fenetre;
      //this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	  }

    private void setSalle(String salle){
      this.titreSalle.setText(salle);
    }

    @FXML
    private void actionAfficher() {
		System.out.println("A faire !");
	}

  @FXML
  private void actionRetour() {
		application.loadParametrageSalles();
	}

}
