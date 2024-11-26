package sae.view;

import java.util.ArrayList;
import java.util.List;

import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.event.Event;
import javafx.event.EventHandler;
import javafx.fxml.FXML;
import javafx.scene.Node;
import javafx.scene.control.Button;
import javafx.scene.control.CheckMenuItem;
import javafx.scene.control.ListView;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import sae.App;
import sae.appli.Salle;
import sae.appli.TypeDonnee;

public class ParametrageChoixSalles {

  private Stage fenetrePrincipale;

  @FXML
  private Button butRecherche;
  @FXML
  private Button butRetour;
  @FXML
  private Button butValider;
  @FXML
  private TextField textRecherche;
  @FXML
  private ListView<Salle> lvSalles;

  @FXML
  private MenuButton choixTypeDonnees;

  private App application;
  private ObservableList<Salle> oListSalles;

  ArrayList<String> choices = new ArrayList<>();

  public void setDatas(Stage fenetre, App app) {
    this.application = app;
    this.fenetrePrincipale = fenetre;
    // this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
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

  public void loadMenuDeroulantDonnees(List<TypeDonnee> listType) {

    CheckMenuItem choix;

    for (int i = 0; i < listType.size(); i++) {
      choix = new CheckMenuItem(listType.get(i).toString());
      choixTypeDonnees.getItems().add(choix);
    }

  }

  @FXML

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
  private void actionRetour() {
    application.loadMenu();
  }

  @FXML
  private void actionRecherche() {
    if (lvSalles!=null) {
      lvSalles.getItems().clear();
    }
    String recherche = textRecherche.getText();
    loadListeSalles(recherche);

  }

  @FXML
  private void actionValider() {
    
    donneeChoisies();
    for (int i = 0; i < choices.size(); i++) {
      System.out.println(choices.get(i));
    }
  }

  public void donneeChoisies() {
    ObservableList<MenuItem> obList = choixTypeDonnees.getItems();
    for (MenuItem n : obList) {
      if (((CheckMenuItem) n).isSelected())
        choices.add(n.getText());
    }
  }
  private void validateComponentState() {
    this.butValider.setDisable(true);
    int selectedIndice = this.lvSalles.getSelectionModel().getSelectedIndex();
    if (selectedIndice >= 0) {
      this.butValider.setDisable(false);
    }

  }
  @Override
  public String toString() {
    return "[Salle: " + this.lvSalles + "]";
  }

}
