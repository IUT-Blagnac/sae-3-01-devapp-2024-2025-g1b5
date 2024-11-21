package sae.view;

import java.util.List;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.stage.Stage;
import sae.App;
import sae.appli.Donnees;
import sae.appli.TypeDonnee;

public class ParametrageChoixSalles {
    
    private Stage fenetrePrincipale ;

    @FXML
    private Button boutton ;
    @FXML
    private Button butRetour ;

    @FXML
    private MenuButton choixTypeDonnees;

    private App application;


    public void setDatas(Stage fenetre,  App app) {
      this.application = app;
      this.fenetrePrincipale = fenetre;
      //this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	  }

    public void loadMenuDeroulant(List<TypeDonnee> listType){
      
      MenuItem choix;

      for (int i=0; i<listType.size(); i++){
        choix = new MenuItem(listType.get(i).toString());
        choixTypeDonnees.getItems().add(choix);
      }
      
    }

    @FXML
    private void actionRetour() {
		  application.loadMenu();
	  }


}
