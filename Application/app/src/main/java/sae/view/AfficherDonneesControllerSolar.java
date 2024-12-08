package sae.view;

import java.io.File;
import java.io.FileReader;
import java.io.IOException;
import java.util.List;
import java.util.Map;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.ColumnConstraints;
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

    @FXML
    private Button afficherGraph;

    private JSONObject solarData; // Champ pour stocker les données JSON

    private List<String> selectedChoices = new ArrayList<>(); // Déclaration de selectedChoices

    private static final Map<String, String> traductionAttributs = new HashMap<>();

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

    
    static {
        // Ajout des traductions pour les attributs
        traductionAttributs.put("lastUpdateTime", "Dernière mise à jour");
        traductionAttributs.put("currentPower", "Puissance actuelle");
        traductionAttributs.put("power", "Puissance");
        traductionAttributs.put("lifeTimeData", "Energie générée depuis le début");
        traductionAttributs.put("lastMonthData", "Energie générée le mois dernier en W");
        traductionAttributs.put("lastYearData", "Energie générée l'année dernière en W");
        // Ajoutez d'autres attributs et leurs traductions ici si nécessaire
    }



    public void initialize() {
        // Définir des contraintes pour les colonnes
        ColumnConstraints col1 = new ColumnConstraints();
        col1.setPercentWidth(25);  // 25% de la largeur totale pour la colonne des clés
        ColumnConstraints col2 = new ColumnConstraints();
        col2.setPercentWidth(35);  // 35% pour la colonne des attributs
        ColumnConstraints col3 = new ColumnConstraints();
        col3.setPercentWidth(40);  // 40% pour la colonne des valeurs
    
        gridDynamique.getColumnConstraints().addAll(col1, col2, col3);  // Ajouter les contraintes au GridPane
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
    
        // Définir un espacement interne pour les cellules
        GridPane.setMargin(titreCle, new javafx.geometry.Insets(5));
        GridPane.setMargin(titreAttribut, new javafx.geometry.Insets(5));
        GridPane.setMargin(titreValeur, new javafx.geometry.Insets(5));
    
        int rowIndex = 1; // Commence après la ligne des titres
    
        // Extraire les clés et les trier dans l'ordre numérique décroissant
        List<Integer> sortedKeys = new ArrayList<>();
        for (Object key : solar.keySet()) {
            sortedKeys.add(Integer.parseInt(key.toString())); // Convertir la clé en entier
        }
        sortedKeys.sort(Collections.reverseOrder()); // Tri décroissant (du plus grand au plus petit)
    
        // Parcourir les clés triées
        for (Integer key : sortedKeys) {
            JSONObject solarEntry = (JSONObject) solar.get(key.toString());
    
            // Ajouter la clé dans la première colonne
            gridDynamique.add(new Label(key.toString()), 0, rowIndex);
    
            // Ajouter les traductions des attributs et les valeurs
            for (String attribut : attributsSelectionnes) {
                Object value = solarEntry.get(attribut); // Récupérer la valeur de l'attribut
                String texte = (value != null ? formatValue(value) : "Non disponible");
    
                // Récupérer le nom en français de l'attribut à partir de la map
                String nomAttribut = traductionAttributs.getOrDefault(attribut, attribut); // Utiliser l'attribut comme fallback si pas trouvé
    
                // Créer un label pour le nom de l'attribut en français et la valeur
                Label labelAttribut = new Label(nomAttribut); // Afficher le nom de l'attribut
                Label valeurLabel = new Label(texte); // Afficher la valeur de l'attribut
    
                // Ajouter les labels dans les bonnes colonnes
                gridDynamique.add(labelAttribut, 1, rowIndex); // Afficher l'attribut
                gridDynamique.add(valeurLabel, 2, rowIndex++); // Afficher la valeur
            }
    
            // Ajouter un espace après chaque entrée de clé pour un meilleur espacement visuel
            gridDynamique.add(new Label(" "), 0, rowIndex++); // Espacement entre les clés
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
            File file = new File("../Iot/solar.json");

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

    @FXML
    private void actionAfficherGraphique() {
        // Créer une nouvelle fenêtre (Stage) pour afficher le graphique
        Stage graphiqueStage = new Stage();  // Nouvelle fenêtre

        // Charger le FXML du graphique
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/graphiqueSolar.fxml"));
            BorderPane vueGraphique = loader.load();

            // Obtenir le contrôleur du graphique
            GraphiqueSolarController graphController = loader.getController();
            graphController.setDatas(graphiqueStage, application);  // Passer la référence de la fenêtre

            // Créer et afficher la nouvelle scène avec le graphique
            Scene scene = new Scene(vueGraphique);
            graphiqueStage.setScene(scene);
            graphiqueStage.setTitle("Graphique Solar");
            graphiqueStage.show();
        } catch (IOException e) {
            System.out.println("Erreur lors de l'ouverture du graphique dans une nouvelle fenêtre : " + e.getMessage());
        }
    }

    
    @FXML
    private void actionAfficherDayGraphique() {
        // Créer une nouvelle fenêtre (Stage) pour afficher le graphique
        Stage graphiqueStage = new Stage();  // Nouvelle fenêtre
    
        try {
            // Créer un FXMLLoader pour charger le fichier FXML
            FXMLLoader loader = new FXMLLoader();
            
            // Charger le fichier FXML
            loader.setLocation(getClass().getResource("/sae/view/graphiqueSolarDay.fxml"));
    
            // Charger le fichier FXML dans la vue
            BorderPane vueGraphique = loader.load();
    
            // Obtenir le contrôleur du graphique
            GraphiqueSolarDayController graphController = loader.getController();
            graphController.setDatas(graphiqueStage);  // Passer la référence de la fenêtre
    
            // Créer et afficher la nouvelle scène avec le graphique
            Scene scene = new Scene(vueGraphique);
            graphiqueStage.setScene(scene);
            graphiqueStage.setTitle("Graphique Energie par Jour");
            graphiqueStage.show();
        } catch (IOException e) {
            // Afficher un message d'erreur si le fichier FXML ne peut pas être chargé
            System.out.println("Erreur lors de l'ouverture du graphique dans une nouvelle fenêtre : " + e.getMessage());
            e.printStackTrace();
        }
    }
    
    
    

    
    @FXML
    private void actionAfficherMonthGraphique() {
        // Créer une nouvelle fenêtre (Stage) pour afficher le graphique
        Stage graphiqueStage = new Stage();  // Nouvelle fenêtre
    
        try {
            // Créer un FXMLLoader pour charger le fichier FXML
            FXMLLoader loader = new FXMLLoader();
            
            // Charger le fichier FXML
            loader.setLocation(getClass().getResource("/sae/view/graphiqueSolarMonth.fxml"));
    
            // Charger le fichier FXML dans la vue
            BorderPane vueGraphique = loader.load();
    
            // Obtenir le contrôleur du graphique
            GraphiqueSolarMonthController graphController = loader.getController();
            graphController.setDatas(graphiqueStage);  // Passer la référence de la fenêtre
    
            // Créer et afficher la nouvelle scène avec le graphique
            Scene scene = new Scene(vueGraphique);
            graphiqueStage.setScene(scene);
            graphiqueStage.setTitle("Graphique Energie par Jour");
            graphiqueStage.show();
        } catch (IOException e) {
            // Afficher un message d'erreur si le fichier FXML ne peut pas être chargé
            System.out.println("Erreur lors de l'ouverture du graphique dans une nouvelle fenêtre : " + e.getMessage());
            e.printStackTrace();
        }
    }
}
