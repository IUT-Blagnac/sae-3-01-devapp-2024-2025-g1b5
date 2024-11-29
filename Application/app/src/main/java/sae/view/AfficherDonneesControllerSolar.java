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
    private Button butRetour;

    @FXML
    private GridPane gridDynamique;

    private JSONObject solarData; // Champ pour stocker les données JSON

    private List<String> selectedChoices = new ArrayList<>(); // Déclaration de selectedChoices

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
    public void setTab(List<String> selectedChoices) {
        this.selectedChoices = selectedChoices; // Stocke les données sélectionnées
        if (selectedChoices.isEmpty()) {
            System.out.println("Aucune donnée sélectionnée !");
        } else {
            System.out.println("Attributs sélectionnés : " + selectedChoices);
        }
    }

    /**
     * Appelé lorsque le bouton "Afficher" est cliqué
     */
    @FXML
    private void actionAfficher() {
        // Charger les données JSON
        chargerFichierSolar();

        if (solarData == null) {
            System.out.println("Aucune donnée chargée depuis le fichier JSON !");
            return;
        }

        if (selectedChoices == null || selectedChoices.isEmpty()) {
            System.out.println("Aucune donnée sélectionnée !");
            return;
        }

        // Filtrer et afficher les données
        afficherDonneesFiltrees(solarData, selectedChoices);
    }

    /**
     * Affiche les données filtrées en fonction des attributs sélectionnés
     */
    private void afficherDonneesFiltrees(JSONObject solarData, List<String> attributsSelectionnes) {
        gridDynamique.getChildren().clear(); // Effacer le contenu précédent de la grille

        JSONObject solar = (JSONObject) solarData.get("solar");
        if (solar == null) {
            System.out.println("Aucune donnée 'solar' trouvée dans le JSON.");
            return;
        }

        // Parcourir les entrées de "solar"
        int rowIndex = 0; // Pour suivre les lignes dans la grille
        for (Object key : solar.keySet()) {
            JSONObject solarEntry = (JSONObject) solar.get(key);

            // Ajouter une étiquette pour chaque entrée principale
            gridDynamique.add(new Label("Données pour la clé : " + key), 0, rowIndex++);

            // Afficher les attributs sélectionnés
            for (String attribut : attributsSelectionnes) {
                Object value = solarEntry.get(attribut); // Récupérer la valeur de l'attribut
                String texte = attribut + " : " + (value != null ? value.toString() : "Non disponible");
                gridDynamique.add(new Label(texte), 0, rowIndex++);
            }
        }
    }

    /**
     * Retour à la page précédente
     */
    @FXML
    private void actionRetour() {
        Stage stage = (Stage) butRetour.getScene().getWindow();
        stage.close();
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
