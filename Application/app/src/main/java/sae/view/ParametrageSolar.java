package sae.view;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;
import javafx.scene.control.CheckMenuItem;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.control.TextArea;
import javafx.scene.shape.Path;
import sae.appli.DonneeSolar;
import java.io.*;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.*;
import java.util.stream.Collectors;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;
import org.json.simple.parser.ParseException;



public class ParametrageSolar {

  private static final String CONFIG_FILE = "iot/config.ini"; 

    
    private Stage fenetrePrincipale ;
    private App application;

    @FXML
    private TextArea dataDisplay;
    
    @FXML
    private MenuButton menuButton;

    @FXML
    private Button butRetour;

    public void setDatas(Stage fenetrePrincipale,  App app) {
        this.application = app;
        this.fenetrePrincipale = fenetrePrincipale;
        // this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
        
        // Remplir le MenuButton avec les éléments de l'enum DonneeSolar
        for (DonneeSolar donnee : DonneeSolar.values()) {
            CheckMenuItem menuItem = new CheckMenuItem(donnee.name());
            menuButton.getItems().add(menuItem);
        }
    }

    @FXML
    private void actionRetour() {
        application.loadMenu();
    }


    public void updateConfig(List<String> donneesSolar) {
      try {
          // Charger toutes les lignes du fichier de configuration avec un chemin direct
          List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
          
          // Créer un StringBuilder pour contenir les nouvelles lignes
          StringBuilder updatedContent = new StringBuilder();
          
          // Créer la nouvelle ligne pour donneesSolar
            String newDonneesSolarLine = "donneesSolar=[" + donneesSolar.stream().map(attr -> "'" + attr + "'") .collect(Collectors.joining(", ")) + "]";
          
          // On parcourt toutes les lignes du fichier pour remplacer celle de "donneesSolar"
          for (String line : lines) {
              if (line.startsWith("donneesSolar")) {
                  // Remplacer la ligne "donneesSolar" par la nouvelle
                  updatedContent.append(newDonneesSolarLine).append("\n");
              } else {
                  // Sinon, on garde la ligne telle quelle
                  updatedContent.append(line).append("\n");
              }
          }
          
          // Réécrire le fichier avec les nouvelles lignes
          Files.write(Paths.get(CONFIG_FILE), updatedContent.toString().getBytes());
          System.out.println("Fichier de configuration mis à jour avec succès.");

      } catch (IOException e) {
          // Gestion des erreurs d'entrée/sortie
          e.printStackTrace();
          System.out.println("Erreur lors de la mise à jour du fichier de configuration.");
      }
  }




    @FXML
private void actionValid() {
    // Étape 1 : Créer une liste pour stocker les éléments sélectionnés
    List<String> selections = new ArrayList<>();

    // Parcourir les éléments du MenuButton
    for (MenuItem item : menuButton.getItems()) {
        // Vérifier si l'élément est un CheckMenuItem
        if (item instanceof CheckMenuItem) {
            CheckMenuItem checkMenuItem = (CheckMenuItem) item; // Cast explicite
            if (checkMenuItem.isSelected()) {
                selections.add(checkMenuItem.getText());
            }
        }
    }

    // Afficher la liste des sélections pour vérifier
    System.out.println("Sélections : " + selections);

    // Appeler une méthode pour mettre à jour la configuration avec ces sélections
    updateConfig(selections);

    // Étape 2 : Charger et afficher les données JSON dans la TextArea
    loadAndDisplaySolarData();
}

    private static final String JSON_FILE = "Iot/solar.json";

    // Méthode pour lire les données JSON et afficher l'élément "6"
    // Méthode pour lire les données JSON et afficher le dernier élément
private void loadAndDisplaySolarData() {
    try {
        // Charger et parser le fichier JSON
        ObjectMapper mapper = new ObjectMapper();
        JsonNode rootNode = mapper.readTree(new File(JSON_FILE));

        // Récupérer le nœud "solar"
        JsonNode solarNode = rootNode.path("solar");

        if (solarNode.isObject()) {
            // Récupérer les clés de l'objet et identifier la dernière
            Iterator<String> fieldNames = solarNode.fieldNames();
            String lastKey = null;

            while (fieldNames.hasNext()) {
                lastKey = fieldNames.next(); // La dernière clé rencontrée dans l'itération
            }

            if (lastKey != null) {
                // Récupérer les données associées à la dernière clé
                JsonNode lastData = solarNode.path(lastKey);

                // Convertir les données en une chaîne JSON formatée
                String formattedData = mapper.writerWithDefaultPrettyPrinter().writeValueAsString(lastData);

                // Afficher les données dans la TextArea
                dataDisplay.setText("Dernière clé : " + lastKey + "\n" + formattedData);
            } else {
                // Si aucune clé n'est trouvée
                dataDisplay.setText("Aucune donnée trouvée dans le fichier JSON.");
            }
        } else {
            // Gérer le cas où "solar" n'est pas un objet JSON valide
            dataDisplay.setText("Le format des données 'solar' est invalide.");
        }
    } catch (IOException e) {
        // Gérer les erreurs d'entrée/sortie
        dataDisplay.setText("Erreur lors du chargement des données : " + e.getMessage());
        e.printStackTrace();
    }
}


}
