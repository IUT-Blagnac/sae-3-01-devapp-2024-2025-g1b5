package sae.view;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.Event;
import javafx.event.EventHandler;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Node;
import javafx.scene.control.Button;
import javafx.scene.control.CheckMenuItem;
import javafx.scene.control.ListView;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.control.TextField;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.App;
import sae.appli.Salle;
import sae.appli.TypeDonnee;

public class ParametrageChoixSalles {
    
    private Stage fenetrePrincipale ;
    private static final String CONFIG_FILE = "Iot/config.ini";

    @FXML
    private Button boutton ;
    @FXML
    private Button butRetour ;
    @FXML
    private Button butValider ;

    @FXML
    private MenuButton choixTypeDonnees;
    
    private String numSalle = "" ;

    private App application;

    ArrayList<String> choices = new ArrayList<>();


    @FXML
      private Button butRecherche;
      @FXML
      private TextField textRecherche;
      @FXML
      private ListView<Salle> lvSalles;

      private ObservableList<Salle> oListSalles;



    public void setDatas(Stage fenetre,  App app) {
      this.application = app;
      this.fenetrePrincipale = fenetre;
      //this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
      this.configure();
	  }

    private void configure() {
      this.oListSalles = FXCollections.observableArrayList();
      this.lvSalles.setItems(this.oListSalles);
      this.lvSalles.getSelectionModel().setSelectionMode(javafx.scene.control.SelectionMode.SINGLE);
      this.lvSalles.getFocusModel().focus(-1);
      this.lvSalles.getSelectionModel().selectedItemProperty().addListener(e -> this.validateComponentState());
      this.validateComponentState();
    }

    public void loadMenuDeroulantDonnees(List<TypeDonnee> listType){
      try {

            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            for (String line : lines) {
                if (line.startsWith("donneesSalles")) {
                    // Extraire la liste des valeurs entre crochets
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    List<String> selectedItems = List.of(values.split(",")).stream().map(v -> v.trim().replace("'", "")).collect(Collectors.toList());
                    for(TypeDonnee donnee : listType){
                      CheckMenuItem cb = new CheckMenuItem(donnee.toString());
                      cb.setUserData(donnee);
                      if (selectedItems.contains(donnee.toString().toLowerCase())) {
                          choixTypeDonnees.getItems().add(cb);
                      }
                    }
                }
              }
            } catch (IOException e) {
                e.printStackTrace();
                System.out.println("Erreur lors du chargement du fichier de configuration : " + e.getMessage());
            }
      
      
    }

    public String getSalle () {
      return this.numSalle;
    }

    public ArrayList<String> getTabDonnee(){
      return choices;
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
      else System.out.println("Selectionner des données ! ");
	  }

    public void donneeChoisies () {
      ObservableList<MenuItem> obList = choixTypeDonnees.getItems();
      for(MenuItem n : obList){
          if(((CheckMenuItem)n).isSelected())
            choices.add(n.getText());
      }
    } 


  public void loadListeSalles(String salle) {
    String[] sls = {
        "B001", "E004", "E106", "Foyer-personnels", "Local-velo", "B202", "C004",
        "B201", "C001", "B109", "Salle-conseil", "B002", "B105", "C101",
        "Foyer-etudiants-entrée", "B234", "B111", "B113", "E006", "E104",
        "E209", "E003", "B217", "C002", "B112", "E001", "B108", "C102",
        "E007", "B203", "E208", "amphi1", "E210", "B103", "E101", "E207",
        "E100", "C006", "hall-amphi", "E102", "hall-entrée-principale",
        "B110", "E103" };

    
    for (int i = 0; i < sls.length; i++) {
      if (salle.equals(null)) {
        Salle s = new Salle(sls[i]);
        lvSalles.getItems().add(s);
      }else{
        if(sls[i].contains(salle)){
          Salle s = new Salle(sls[i]);
          lvSalles.getItems().add(s);
        }
      }
    }
  }


  @FXML
  private void actionRecherche() {
    if (lvSalles!=null) {
      lvSalles.getItems().clear();
    }
    String recherche = textRecherche.getText();
    loadListeSalles(recherche);

  }


  private void validateComponentState() {
    this.butValider.setDisable(true);
    int selectedIndice = this.lvSalles.getSelectionModel().getSelectedIndex();
    if (selectedIndice >= 0) {
      this.butValider.setDisable(false);
    }

  }

    


}
