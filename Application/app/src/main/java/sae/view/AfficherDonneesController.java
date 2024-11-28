package sae.view;

import java.io.File;
import java.io.FileReader;
import java.net.URL;
import java.nio.file.Paths;
import java.util.ArrayList;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import sae.App;

public class AfficherDonneesController {

  @SuppressWarnings("unused")
  private Stage fenetrePrincipale;

  private App application;

  @FXML
  private Label titreSalle;

  @FXML
  private GridPane gridDynamique;

  private ArrayList<String> donnees = new ArrayList<>();

  public void setDatas(Stage fenetre, App app) {
    this.application = app;
    this.fenetrePrincipale = fenetre;
  }

  public void setSalle(String salle) {
    this.titreSalle.setText(salle);
  }

  public void setTab(ArrayList<String> list) {
    this.donnees = list;
  }

  @FXML
  private void actionAfficher() {
    System.out.println("A faire !");
    // lecture();
    System.out.println(donnees);
    chargerFichierSalle();
  }

  @FXML
  private void actionRetour() {
    application.loadParametrageSalles();
  }

  public void afficherDonnees() {
    for (int i = 0; i < donnees.size(); i++) {
      gridDynamique.add(new Label(donnees.get(i) + " :"), 0, i);
    }
  }

  public void chargerFichierSalle() {

    JSONParser parser = new JSONParser();

    try {
      // Lire le fichier JSON
      URL resource = getClass().getClassLoader().getResource("Iot/salles.json");

      if (resource == null) {
        System.out.println("Le fichier salles.json est introuvable.");
        return;
      }

      FileReader reader = new FileReader(Paths.get(resource.toURI()).toFile());
      JSONObject jsonObject = (JSONObject) parser.parse(reader);

      // Test: Afficher le contenu du fichier JSON
      System.out.println("Fichier chargé avec succès : ");
      // System.out.println(jsonObject.toJSONString()); // Affiche le contenu du JSON
      // en format lisible

      // Recherche de la salle B110
      if (jsonObject.containsKey("E004")) {
        JSONObject salleB110 = (JSONObject) jsonObject.get("E004");
        System.out.println(salleB110.toJSONString());
      } else {
        System.out.println("La salle E004 n'existe pas dans le fichier JSON.");
      }

    } catch (Exception e) {
      e.printStackTrace();
    }

  }

  public void modifConfig() {

  }

  public void lecture() {

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
