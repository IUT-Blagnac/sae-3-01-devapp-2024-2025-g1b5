package sae.view;

import java.io.File;
import java.io.FileReader;
import java.net.URL;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.List;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.scene.control.Label;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import sae.App;

public class AfficherDonneesControllerSolar {

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

  public void setTab(List<String> selectedData) {
    // Votre code pour afficher les données dans le GridPane
    for (int i = 0; i < selectedData.size(); i++) {
        String data = selectedData.get(i);
        Label label = new Label(data);
        gridDynamique.add(label, 0, i);  // Ajoutez à la première colonne et à la ligne i
    }
}


  @FXML
  private void actionAfficher() {
      // Charger les données depuis le fichier JSON
      chargerFichierSolar();
      // Afficher les données dans le GridPane
      afficherDonnees();
  }


  @FXML
  private void actionRetour() {
    application.loadParametrageSolar();
  }

  public void afficherDonnees() {
    gridDynamique.getChildren().clear(); // Clear any existing labels in the grid

    for (int i = 0; i < donnees.size(); i++) {
        Label label = new Label(donnees.get(i));
        gridDynamique.add(label, 0, i);  // Add the label in the first column, row i
    }
}



public void chargerFichierSolar() {
    // Crée un objet JSONParser pour analyser le fichier JSON
    JSONParser parser = new JSONParser();

    try {
        // Définir le chemin du fichier solar.json à la racine du projet
        File file = new File("Iot/solar.json");

        // Vérifier si le fichier existe à la racine du projet
        if (!file.exists()) {
            System.out.println("Le fichier solar.json est introuvable à la racine du projet.");
            return;  // Arrêter l'exécution si le fichier n'est pas trouvé
        }

        // Utiliser un FileReader pour lire le fichier
        FileReader reader = new FileReader(file);

        // Analyser le contenu du fichier JSON
        JSONObject json = (JSONObject) parser.parse(reader);

        // Afficher le contenu du fichier JSON (par exemple)
        System.out.println("Données extraites du fichier solar.json :");
        System.out.println(json.toJSONString());

        // Fermer le FileReader après utilisation
        reader.close();
        
        // Ici vous pouvez traiter les données extraites selon vos besoins
        // Par exemple, récupérer des valeurs spécifiques :
        String currentPower = (String) json.get("currentPower");
        String lastUpdateTime = (String) json.get("lastUpdateTime");
        
        // Affichage des valeurs extraites
        System.out.println("currentPower : " + currentPower);
        System.out.println("lastUpdateTime : " + lastUpdateTime);
        
        // Vous pouvez maintenant utiliser ces données dans votre application
        // Par exemple, ajouter à une liste, une table ou une autre structure de données
    } catch (Exception e) {
        // Gérer les exceptions (erreurs de lecture, parsing, etc.)
        System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
        e.printStackTrace();  // Afficher la trace d'erreur pour le débogage
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
