package sae.view;

import java.io.File;
import java.io.FileReader;
import java.net.URL;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.Set;

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

  private String numSalle;

  @FXML
  private GridPane gridDynamique;

  private ArrayList<String> donnees = new ArrayList<>();

  private JSONObject sallesData; // Champ pour stocker les données JSON


  public void setDatas(Stage fenetre, App app) {
    this.application = app;
    this.fenetrePrincipale = fenetre;
  }

  public void setSalle(String salle) {
    this.titreSalle.setText(salle);
    this.numSalle = salle;
  }

  public void setTab(ArrayList<String> list) {
    this.donnees = list;
  }

  @FXML
  private void actionAfficher() {
    System.out.println("A faire !");
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
            // Définir le chemin du fichier salles.json à la racine du projet
            File file = new File("Iot/salles.json");

            if (!file.exists()) {
                System.out.println("Le fichier salles.json est introuvable à la racine du projet.");
                return;
            }

            // Lire et analyser le fichier JSON
            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            // Stocker les données dans le champ solarData
            this.sallesData = json;

            System.out.println("Données extraites du fichier salles.json " + numSalle + " : ");
            //System.out.println(json.toJSONString());
            

            if (json.containsKey(numSalle)) {
              JSONObject salleChoisi = (JSONObject) json.get(numSalle);
              System.out.println(salleChoisi.toJSONString());

              // Récupérer toutes les valeurs pour cette clé spécifique
              Set<String> allKeys = salleChoisi.keySet();

              JSONObject dernierClé = (JSONObject) salleChoisi.get( (allKeys.size() - 1) + "" );
              System.out.println( "Dernière clée de la salle : " + (allKeys.size() - 1));
              System.out.println(dernierClé);

            } else {
              System.out.println("La salle " + numSalle + " n'existe pas dans le fichier JSON.");
            }

          

            reader.close();

        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
            e.printStackTrace();
        }

  }

}
