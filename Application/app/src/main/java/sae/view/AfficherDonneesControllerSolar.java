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
  JSONParser parser = new JSONParser();

  try {
      // Lire le fichier JSON
      URL resource = getClass().getClassLoader().getResource("sae/iot/solar.json");

      if (resource == null) {
          System.out.println("Le fichier solar.json est introuvable.");
          return;
      }

      FileReader reader = new FileReader(Paths.get(resource.toURI()).toFile());
      JSONObject jsonObject = (JSONObject) parser.parse(reader);

      // Vérification de la clé 'solar' et extraction des données
      if (jsonObject.containsKey("solar")) {
          JSONObject solarData = (JSONObject) jsonObject.get("solar");

          // Boucle pour parcourir chaque capteur sous la clé 'solar'
          for (Object key : solarData.keySet()) {
              JSONObject sensorData = (JSONObject) solarData.get(key);

              // Vérification des choix sélectionnés
              if (donnees.contains("Current Power") && sensorData.containsKey("currentPower")) {
                  JSONObject currentPower = (JSONObject) sensorData.get("currentPower");
                  donnees.add("Current Power: " + currentPower.get("power"));
              }

              if (donnees.contains("Last Day Energy") && sensorData.containsKey("lastDayData")) {
                  JSONObject lastDayData = (JSONObject) sensorData.get("lastDayData");
                  donnees.add("Last Day Energy: " + lastDayData.get("energy"));
              }

              if (donnees.contains("Last Month Energy") && sensorData.containsKey("lastMonthData")) {
                  JSONObject lastMonthData = (JSONObject) sensorData.get("lastMonthData");
                  donnees.add("Last Month Energy: " + lastMonthData.get("energy"));
              }

              if (donnees.contains("Last Year Energy") && sensorData.containsKey("lastYearData")) {
                  JSONObject lastYearData = (JSONObject) sensorData.get("lastYearData");
                  donnees.add("Last Year Energy: " + lastYearData.get("energy"));
              }

              if (donnees.contains("Life Time Energy") && sensorData.containsKey("lifeTimeData")) {
                  JSONObject lifeTimeData = (JSONObject) sensorData.get("lifeTimeData");
                  donnees.add("Life Time Energy: " + lifeTimeData.get("energy"));
              }

              if (donnees.contains("Last Update Time") && sensorData.containsKey("lastUpdateTime")) {
                  donnees.add("Last Update Time: " + sensorData.get("lastUpdateTime"));
              }
          }

          // Appel pour afficher les données dans la grille dynamique
          afficherDonnees();
      } else {
          System.out.println("Aucune donnée solar trouvée dans le fichier JSON.");
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
