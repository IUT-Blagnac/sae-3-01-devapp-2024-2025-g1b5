package sae.view;

import java.io.File;
import java.io.FileReader;
import java.net.URL;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;
import java.util.Set;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
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

  Map<String, Object> dicoTypeValeur ; //recupere toutes les valeurs du config.ini
  Map<String, Object> dicoGraphe ; //recupere seulement les données selectionnées

  Map<String, Map<String, Object> > dicoHist ; //recupere l'historique des données


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
    System.out.println(dicoHist);
      application.loadGraphe(numSalle, dicoGraphe);
      application.loadGraphe2(numSalle, dicoHist);
  }

  @FXML
  private void actionRetour() {
    application.loadParametrageSalles();
  }

  public void afficherDonnees() {
    chargerFichierSalle();
    dicoGraphe = new HashMap<String,Object>();

    for (int i = 0; i < donnees.size(); i++) { 
      gridDynamique.add(new Label( donnees.get(i).toUpperCase() + " :"), 0, i);
      gridDynamique.add(new Label( dicoTypeValeur.get(donnees.get(i)) + "" ), 1, i);

      dicoGraphe.put(donnees.get(i), dicoTypeValeur.get(donnees.get(i)));
    }
  
    for (Map.Entry<String, Map<String, Object>> entry1 : dicoHist.entrySet()) {
      entry1.getValue().entrySet().removeIf((entry) -> {
        return !donnees.contains(entry.getKey());
      }); //retire les valeurs qui n'existe pas dans le tableau
    }


  }

  public void chargerFichierSalle() {

     JSONParser parser = new JSONParser();

        try {
            File file = new File("Iot/salles.json");

            if (!file.exists()) {
                Alert alert = new Alert(AlertType.ERROR, "Erreur : Le fichier salles.json est introuvable !");
                alert.show();
            }

            // Lire et analyser le fichier JSON
            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            // Stocker les données dans la variable
            this.sallesData = json;            

            if (json.containsKey(numSalle)) {
              JSONObject salleChoisie = (JSONObject) json.get(numSalle);
              this.dicoHist = salleChoisie; 
              //attribut toutes les données existantes dans un dictionnaire

              // Récupérer toutes les valeurs pour cette clé spécifique
              Set<String> allKeys = salleChoisie.keySet();

              JSONObject dernierClé = (JSONObject) salleChoisie.get( (allKeys.size() - 1) + "" );
              this.dicoTypeValeur = dernierClé;  
              //récupère les dernières données de la salle et les attributs à un dictionnaire

            } else {
               Alert alert = new Alert(AlertType.ERROR, "Erreur : La salle n'existe pas !");
               alert.show();
            }

            reader.close();
            
        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier salles.json : " + e.getMessage());
            e.printStackTrace();
        }

  }

}
