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
  private void actionAfficher() {;
    
  }

  @FXML
  private void actionRetour() {
    application.loadParametrageSalles();
  }

  public void afficherDonnees() {
    Map<String, Double> dicoTypeValeur = chargerFichierSalle();
    System.out.println(dicoTypeValeur);

    System.out.println(donnees);

    for (int i = 0; i < donnees.size(); i++) { 
      gridDynamique.add(new Label( donnees.get(i).toUpperCase() + " :"), 0, i);
      gridDynamique.add(new Label( dicoTypeValeur.get(donnees.get(i)) + "" ), 1, i);
    }
  }

  public Map<String, Double> chargerFichierSalle() {

     JSONParser parser = new JSONParser();
     Map<String, Double> dicoTypeValeur = new HashMap<String,Double>();

        try {
            // Définir le chemin du fichier salles.json à la racine du projet
            File file = new File("Iot/salles.json");

            if (!file.exists()) {
                System.out.println("Le fichier salles.json est introuvable à la racine du projet.");
                // return;
            }

            // Lire et analyser le fichier JSON
            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            // Stocker les données dans le champ solarData
            this.sallesData = json;

            System.out.println("Données extraites du fichier salles.json " + numSalle + " : ");
            //System.out.println(json.toJSONString());
            

            if (json.containsKey(numSalle)) {
              JSONObject salleChoisie = (JSONObject) json.get(numSalle);
              System.out.println(salleChoisie.toJSONString());

              // Récupérer toutes les valeurs pour cette clé spécifique
              Set<String> allKeys = salleChoisie.keySet();

              JSONObject dernierClé = (JSONObject) salleChoisie.get( (allKeys.size() - 1) + "" );
              System.out.println( "Dernière clée de la salle : " + (allKeys.size() - 1));
              System.out.println(dernierClé);

              dicoTypeValeur = dernierClé;
    

            } else {
              System.out.println("La salle " + numSalle + " n'existe pas dans le fichier JSON.");
            }

            reader.close();
            
            

        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
            e.printStackTrace();
        }

        return dicoTypeValeur;

  }

}
