package sae.view;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import javafx.collections.ObservableList;
import javafx.event.Event;
import javafx.event.EventHandler;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.control.Button;
import javafx.scene.control.CheckMenuItem;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.App;
import sae.appli.TypeDonnee;

public class ParametrageChoixSalles {
    
    private Stage fenetrePrincipale ;

    @FXML
    private Button boutton ;
    @FXML
    private Button butRetour ;
    @FXML
    private Button butValider ;

    @FXML
    private MenuButton choixTypeDonnees;
    
    private String numSalle ;

    private App application;

    ArrayList<String> choices = new ArrayList<>();


    public void setDatas(Stage fenetre,  App app) {
      this.application = app;
      this.fenetrePrincipale = fenetre;
      //this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	  }

    public void loadMenuDeroulantDonnees(List<TypeDonnee> listType){
      
      CheckMenuItem choix;

      for (int i=0; i<listType.size(); i++){
        choix = new CheckMenuItem(listType.get(i).toString());
        choixTypeDonnees.getItems().add(choix);
      }
      
    }

    @FXML
    private void actionRetour() {
		  application.loadMenu();
	  }

    @FXML
    private void actionValider() {
		  donneeChoisies();
      if (!choices.isEmpty())
        application.loadDonnees();
	  }

    public void donneeChoisies () {
      ObservableList<MenuItem> obList = choixTypeDonnees.getItems();
      for(MenuItem n : obList){
          if(((CheckMenuItem)n).isSelected())
            choices.add(n.getText());
      }
    }

    


}
