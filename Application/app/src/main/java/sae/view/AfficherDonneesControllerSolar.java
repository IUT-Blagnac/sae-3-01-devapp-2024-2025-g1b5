package sae.view;

import java.io.File;
import java.io.FileReader;
import java.util.List;
import java.util.ArrayList;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
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
    Button butRetour;

    @FXML
    private GridPane gridDynamique;

    private JSONObject solarData; // Champ pour stocker les données JSON

    private ArrayList<String> donnees = new ArrayList<>();

    /**
     * Configure les données de la fenêtre principale et de l'application
     */
    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }

    /**
     * Définit le titre de la salle
     */
    public void setSalle(String salle) {
        this.titreSalle.setText(salle);
    }

    /**
     * Remplit la grille dynamique avec des données sélectionnées
     */
    public void setTab(List<String> selectedData) {
        // Afficher les données dans le GridPane
        gridDynamique.getChildren().clear(); // Efface tout contenu précédent
        for (int i = 0; i < selectedData.size(); i++) {
            String data = selectedData.get(i);
            Label label = new Label(data);
            gridDynamique.add(label, 0, i); // Ajoute à la première colonne et la ligne i
        }
    }

    /**
     * Appelé lorsque le bouton "Afficher" est cliqué
     */
    @FXML
    private void actionAfficher() {
        // Charger les données depuis le fichier JSON
        chargerFichierSolar();
        // Afficher les données dans le GridPane
        if (solarData != null) { // Vérifie que les données ont bien été chargées
            afficherDonnees(solarData);
        }
    }

    /**
     * Retour à la page précédente
     */
    @FXML
    private void actionRetour() {
        application.loadParametrageSolar();
    }

    /**
     * Affiche les données JSON dans le GridPane
     */
    public void afficherDonnees(JSONObject solarData) {
        gridDynamique.getChildren().clear(); // Effacer tout contenu précédent dans la grille

        // Parcourir chaque entrée dans l'objet "solar"
        JSONObject solar = (JSONObject) solarData.get("solar");
        if (solar == null) {
            System.out.println("Aucune donnée 'solar' trouvée dans le JSON.");
            return;
        }

        for (Object key : solar.keySet()) {
            JSONObject solarEntry = (JSONObject) solar.get(key);

            // Extraire les champs de chaque entrée
            String lastUpdateTime = (String) solarEntry.get("lastUpdateTime");
            JSONObject currentPowerObj = (JSONObject) solarEntry.get("currentPower");
            JSONObject lastYearDataObj = (JSONObject) solarEntry.get("lastYearData");
            JSONObject lastMonthDataObj = (JSONObject) solarEntry.get("lastMonthData");
            JSONObject lastDayDataObj = (JSONObject) solarEntry.get("lastDayData");
            JSONObject lifeTimeDataObj = (JSONObject) solarEntry.get("lifeTimeData");

            // Obtenir les valeurs
            String currentPower = currentPowerObj != null ? currentPowerObj.get("power").toString() : "N/A";
            String lastYearEnergy = lastYearDataObj != null ? lastYearDataObj.get("energy").toString() : "N/A";
            String lastMonthEnergy = lastMonthDataObj != null ? lastMonthDataObj.get("energy").toString() : "N/A";
            String lastDayEnergy = lastDayDataObj != null ? lastDayDataObj.get("energy").toString() : "N/A";
            String lifeTimeEnergy = lifeTimeDataObj != null ? lifeTimeDataObj.get("energy").toString() : "N/A";

            // Ajouter les données dans la GridPane
            int rowIndex = gridDynamique.getRowCount(); // Index de la ligne suivante

            gridDynamique.add(new Label("Données pour la clé : " + key), 0, rowIndex++);
            gridDynamique.add(new Label("Last Update Time : " + lastUpdateTime), 0, rowIndex++);
            gridDynamique.add(new Label("Current Power : " + currentPower), 0, rowIndex++);
            gridDynamique.add(new Label("Last Year Energy : " + lastYearEnergy), 0, rowIndex++);
            gridDynamique.add(new Label("Last Month Energy : " + lastMonthEnergy), 0, rowIndex++);
            gridDynamique.add(new Label("Last Day Energy : " + lastDayEnergy), 0, rowIndex++);
            gridDynamique.add(new Label("Life Time Energy : " + lifeTimeEnergy), 0, rowIndex++);
        }
    }

    /**
     * Charge le fichier JSON et stocke les données dans le champ solarData
     */
    public void chargerFichierSolar() {
        JSONParser parser = new JSONParser();

        try {
            // Définir le chemin du fichier solar.json à la racine du projet
            File file = new File("Iot/solar.json");

            if (!file.exists()) {
                System.out.println("Le fichier solar.json est introuvable à la racine du projet.");
                return;
            }

            // Lire et analyser le fichier JSON
            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            // Stocker les données dans le champ solarData
            this.solarData = json;

            System.out.println("Données extraites du fichier solar.json :");
            System.out.println(json.toJSONString());
            reader.close();

        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
            e.printStackTrace();
        }
    }
}
