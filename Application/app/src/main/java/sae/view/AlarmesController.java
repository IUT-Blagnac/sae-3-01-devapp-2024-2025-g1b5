package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import sae.App;
import sae.appli.TypeDonnee;
import sae.appli.Alarme;
import sae.appli.Alarmes;

import java.io.IOException;
import java.util.*;

public class AlarmesController {

    @FXML
    private DatePicker datePicker; // Sélection de la date
    @FXML
    private ComboBox<String> salleComboBox; // ComboBox pour la salle
    @FXML
    private ComboBox<TypeDonnee> elementComboBox; // ComboBox pour l'élément recherché (TypeDonnee directement)
    @FXML
    private Button rechercheButton; // Bouton Recherche
    @FXML
    private Button maxButton; // Bouton Max
    @FXML
    private Button minButton; // Bouton Min
    @FXML
    private Button retourButton; // Bouton Retour
    @FXML
    private VBox resultVBox; // VBox pour afficher les résultats

    private App application;
    private Stage fenetrePrincipale;

    private Alarmes alarmes; // Objet Alarmes pour gérer les données

    private List<String> salles = new ArrayList<>(); // Liste dynamique de salles
    private Boolean selectedMax = null; // État des boutons "Max" et "Min", initialisé à null (aucun sélectionné)

    // Méthode d'initialisation
    @FXML
    public void initialize() {
        alarmes = new Alarmes(); // Initialisation de l'objet Alarmes

        // Charger les données depuis le fichier JSON
        try {
            alarmes.loadFromJson("C:\\Users\\Etudiant\\Documents\\GitHub\\sae-3-01-devapp-2024-2025-g1b5\\Application\\app\\src\\main\\resources\\sae\\iot\\alarmes.json"); // Remplacez le chemin par le chemin correct
        } catch (IOException e) {
            e.printStackTrace();
            showAlert("Erreur", "Impossible de charger les données d'alarmes.", Alert.AlertType.ERROR);
        }

        // Initialisation des éléments de la ComboBox pour l'élément recherché (TypeDonnee)
        elementComboBox.getItems().addAll(TypeDonnee.values());

        // Initialisation dynamique des salles dans le ComboBox
        loadSalles();
        salleComboBox.getItems().addAll(salles);

        // Gérer l'action des boutons
        maxButton.setOnAction(e -> handleMax());
        minButton.setOnAction(e -> handleMin());
        rechercheButton.setOnAction(e -> handleRecherche());
        retourButton.setOnAction(e -> handleRetour());
    }

    // Méthode pour charger les salles dynamiquement
    private void loadSalles() {
        String[] sls = {
            "B001", "E004", "E106", "Foyer-personnels", "Local-velo", "B202", "C004",
            "B201", "C001", "B109", "Salle-conseil", "B002", "B105", "C101",
            "Foyer-etudiants-entrée", "B234", "B111", "B113", "E006", "E104",
            "E209", "E003", "B217", "C002", "B112", "E001", "B108", "C102",
            "E007", "B203", "E208", "amphi1", "E210", "B103", "E101", "E207",
            "E100", "C006", "hall-amphi", "E102", "hall-entrée-principale",
            "B110", "E103"
        };
        salles.addAll(Arrays.asList(sls));
    }

    // Méthode pour récupérer toutes les alarmes
    public Map<String, List<Alarme>> getAllAlarmes() {
        return alarmes.getAlarmes();
    }

    // Méthode pour afficher un message d'alerte
    private void showAlert(String title, String content, Alert.AlertType alertType) {
        Alert alert = new Alert(alertType);
        alert.setTitle(title);
        alert.setContentText(content);
        alert.showAndWait();
    }

    // Méthode pour gérer la sélection "Max"
    private void handleMax() {
        if (selectedMax == null || selectedMax == false) {
            selectedMax = true;
            updateButtonStyles();
        } else {
            selectedMax = null;
            updateButtonStyles();
        }
    }

    // Méthode pour gérer la sélection "Min"
    private void handleMin() {
        if (selectedMax == null || selectedMax == true) {
            selectedMax = false;
            updateButtonStyles();
        } else {
            selectedMax = null;
            updateButtonStyles();
        }
    }

    // Méthode pour mettre à jour les styles des boutons
    private void updateButtonStyles() {
        if (selectedMax == null) {
            maxButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
            minButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
        } else if (selectedMax) {
            maxButton.setStyle("-fx-background-color: green; -fx-text-fill: white;");
            minButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
        } else {
            minButton.setStyle("-fx-background-color: red; -fx-text-fill: white;");
            maxButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
        }
    }

    // Méthode pour effectuer la recherche des alarmes en fonction des critères sélectionnés
    private void handleRecherche() {
        // Récupérer les critères de recherche
        String salle = salleComboBox.getValue(); // Salle sélectionnée
        String date = (datePicker.getValue() != null) ? datePicker.getValue().toString() : null; // Date sélectionnée
        TypeDonnee typeDonnee = elementComboBox.getValue(); // Type de donnée (ex: temperature, co2)
        Boolean isMax = selectedMax; // Filtre Max ou Min
    
        // Récupérer toutes les alarmes
        Map<String, List<Alarme>> allAlarmes = alarmes.getAlarmes();
    
        // Liste pour stocker les résultats filtrés
        List<Alarme> resultats = new ArrayList<>();
    
        // Filtrer les alarmes
        for (Map.Entry<String, List<Alarme>> entry : allAlarmes.entrySet()) {
            // Vérifier si on doit filtrer par salle
            if (salle != null && !salle.equals(entry.getKey())) {
                continue; // Ignorer les salles non correspondantes
            }
    
            // Parcourir les alarmes de cette salle
            for (Alarme alarme : entry.getValue()) {
                boolean correspond = true;
    
                // Filtrer par date
                if (date != null && !alarme.getTimestamp().startsWith(date)) {
                    correspond = false;
                }
    
                // Filtrer par type de donnée
                if (typeDonnee != null && !alarme.getKey().equals(typeDonnee.toString().toLowerCase())) {
                    correspond = false;
                }
    
                // Filtrer par Max ou Min
                if (isMax != null) {
                    if (isMax && !alarme.getAlarmType().equals("MAX")) {
                        correspond = false;
                    }
                    if (!isMax && !alarme.getAlarmType().equals("MIN")) {
                        correspond = false;
                    }
                }
    
                // Ajouter l'alarme si elle correspond aux critères
                if (correspond) {
                    resultats.add(alarme);
                }
            }
        }
    
        // Afficher les résultats
        displayResults(resultats);
    }
    
    // Méthode pour afficher les résultats dans la VBox
    private void displayResults(List<Alarme> alarmes) {
        resultVBox.getChildren().clear(); // Effacer les résultats précédents

        if (alarmes.isEmpty()) {
            resultVBox.getChildren().add(new Label("Aucun résultat trouvé."));
        } else {
            for (Alarme alarme : alarmes) {
                // Conteneur horizontal pour formater chaque alarme
                HBox alarmeBox = new HBox(10); // Espacement de 10 entre les éléments
                alarmeBox.setStyle("-fx-padding: 10; -fx-background-color: #f4f4f4; -fx-border-color: #ccc; -fx-border-radius: 5; -fx-background-radius: 5;");

                // Labels pour chaque champ de l'alarme
                Label keyLabel = new Label("Type: " + alarme.getKey());
                keyLabel.setStyle("-fx-font-weight: bold;");

                Label valueLabel = new Label("Valeur: " + alarme.getValue());

                Label typeLabel = new Label("Alarme: " + alarme.getAlarmType());
                typeLabel.setStyle(alarme.getAlarmType().equals("MAX") ? "-fx-text-fill: red;" : "-fx-text-fill: blue;");

                Label timestampLabel = new Label("Date: " + alarme.getTimestamp());
                timestampLabel.setStyle("-fx-font-style: italic;");

                // Ajouter les labels au conteneur horizontal
                alarmeBox.getChildren().addAll(keyLabel, valueLabel, typeLabel, timestampLabel);

                // Ajouter le conteneur horizontal à la VBox
                resultVBox.getChildren().add(alarmeBox);
            }
        }
    }

    

    // Méthode pour gérer le retour
    @FXML
    private void handleRetour() {
        application.loadMenu();
    }

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }
}
