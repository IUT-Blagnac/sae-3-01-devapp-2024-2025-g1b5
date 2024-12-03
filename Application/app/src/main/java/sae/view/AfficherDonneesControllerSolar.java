package sae.view;

import java.io.File;
import java.io.FileReader;
import java.util.List;
import java.util.Map;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import sae.App;
import sae.view.AfficherGraphiqueControllerSolar;

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

    @FXML
    private Button afficherGraph;

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

    // Titre des colonnes
    Label titreCle = new Label("Clé");
    Label titreAttribut = new Label("Attribut");
    Label titreValeur = new Label("Valeur");
    gridDynamique.add(titreCle, 0, 0);
    gridDynamique.add(titreAttribut, 1, 0);
    gridDynamique.add(titreValeur, 2, 0);
    
    // Mettre en valeur les titres
    titreCle.setStyle("-fx-font-weight: bold;");
    titreAttribut.setStyle("-fx-font-weight: bold;");
    titreValeur.setStyle("-fx-font-weight: bold;");
    
    int rowIndex = 1; // Commence après la ligne des titres

    // Utilisation d'un Set pour éviter d'afficher les clés en double
    Set<String> displayedKeys = new HashSet<>();
    
    // Trier les entrées par clé dans l'ordre décroissant des valeurs numériques
    List<Map.Entry<String, JSONObject>> sortedEntries = new ArrayList<>(solar.entrySet());
    sortedEntries.sort((entry1, entry2) -> {
        try {
            // Convertir les clés en entiers pour effectuer un tri numérique
            Integer key1 = Integer.valueOf(entry1.getKey());
            Integer key2 = Integer.valueOf(entry2.getKey());
            return key2.compareTo(key1); // Ordre décroissant
        } catch (NumberFormatException e) {
            // Si les clés ne sont pas des entiers valides, les laisser dans l'ordre d'origine
            return entry1.getKey().compareTo(entry2.getKey());
        }
    });

    // Ajouter les entrées triées à la grille
    for (Map.Entry<String, JSONObject> entry : sortedEntries) {
        String key = entry.getKey();
        JSONObject solarEntry = entry.getValue();
        gridDynamique.add(new Label(key), 0, rowIndex);
        displayedKeys.add(key);  // Ajouter la clé au Set pour éviter les doublons

        // Ajouter les attributs sélectionnés
        for (String attribut : attributsSelectionnes) {
            Object value = solarEntry.get(attribut); // Récupérer la valeur de l'attribut
            String texte = attribut + " : " + (value != null ? formatValue(value) : "Non disponible");

            // Mettre les données dans des colonnes séparées
            gridDynamique.add(new Label(attribut), 1, rowIndex);
            gridDynamique.add(new Label(texte), 2, rowIndex++);
        }

        // Ajouter un espace après chaque entrée de clé pour un meilleur espacement visuel
        gridDynamique.add(new Label(" "), 0, rowIndex++);  // Espacement entre les clés
    }
}


    /**
     * Formate la valeur pour éviter d'afficher les accolades des objets JSON
     */
    private String formatValue(Object value) {
        if (value instanceof JSONObject) {
            // Convertir l'objet JSON en chaîne de caractères sans accolades
            return value.toString().replaceAll("[{}]", "").trim();
        }
        return value != null ? value.toString() : "Non disponible";
    }

    @FXML
    private void actionAfficherGraphique() {
        if (solarData == null) {
            System.out.println("Aucune donnée disponible pour afficher le graphique !");
            return;
        }
    
        // Création du contrôleur pour afficher le graphique
        AfficherGraphiqueControllerSolar graphController = new AfficherGraphiqueControllerSolar(solarData);
        graphController.afficherGraphique(); // Affichage du graphique
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
