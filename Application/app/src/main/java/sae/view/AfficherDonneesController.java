package sae.view;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import sae.App;

public class AfficherDonneesController {

    private Stage fenetrePrincipale ;

    private App application;

    @FXML
    private Label titreSalle;

    @FXML
    private GridPane gridDynamique ;

    private ArrayList<String> donnees = new ArrayList<>();

    
    public void setDatas(Stage fenetre,  App app) {
      this.application = app;
      this.fenetrePrincipale = fenetre;
      //this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
	  }

    public void setSalle(String salle){
      this.titreSalle.setText(salle);
    }

    public void setTab( ArrayList<String> list){
      this.donnees = list ;
    }

    @FXML
    private void actionAfficher() {
		  System.out.println("A faire !");
      //lecture();
	  }

    @FXML
    private void actionRetour() {
      application.loadParametrageSalles();
    }

    private void afficher(){

    }







    public void lecture(){
   
       // Chemin relatif du fichier Python
       String pythonScriptPath = "main2.py"; // Le fichier Python est dans le même dossier

       // Créer un objet File avec le chemin relatif
       File file = new File(pythonScriptPath);

       // Vérifier si le fichier existe
       if (file.exists()) 
           System.out.println("Le fichier Python existe : " + pythonScriptPath); 
        else 
           System.out.println("nexiste pas");     

      
      
    }

}
                      