package sae.view;

import javafx.beans.property.SimpleObjectProperty;
import javafx.beans.property.SimpleStringProperty;
import javafx.fxml.FXML;
import javafx.scene.control.*;
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
    private ComboBox<String> elementComboBox; // Change this to ComboBox<String>; // ComboBox pour l'élément recherché (TypeDonnee directement)
    @FXML
    private Button rechercheButton; // Bouton Recherche
    @FXML
    private Button maxButton; // Bouton Max
    @FXML
    private Button minButton; // Bouton Min
    @FXML
    private Button retourButton; // Bouton Retour
    @FXML
    private TableView<Alarme> resultTable;

    @FXML
    private TableColumn<Alarme, String> dateColumn;

    @FXML
    private TableColumn<Alarme, String> typeColumn;

    @FXML
    private TableColumn<Alarme, Double> valueColumn;

    @FXML
    private TableColumn<Alarme, String> alarmTypeColumn;

    @FXML
    private TableColumn<Alarme, String> salleColumn; // Colonne pour la salle

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
            alarmes.loadFromJson("../../Iot/alarmes.json");
        } catch (IOException e) {
            e.printStackTrace();
            showAlert("Erreur", "Impossible de charger les données d'alarmes.", Alert.AlertType.ERROR);
        }

        // Initialisation de l'élément recherché ComboBox avec "Tous" et les valeurs de TypeDonnee
        elementComboBox.getItems().add("ALL"); // Ajouter "Tous"
        for (TypeDonnee type : TypeDonnee.values()) {
            elementComboBox.getItems().add(type.toString()); // Ajouter chaque valeur de TypeDonnee
        }

        // Initialisation dynamique des salles dans le ComboBox
        loadSalles();
        salleComboBox.getItems().add("ALL"); // Ajouter "Tous" à la ComboBox de salle
        salleComboBox.getItems().addAll(salles); // Ajouter toutes les salles

        // Configuration des colonnes du TableView
        dateColumn.setCellValueFactory(cellData -> new SimpleStringProperty(cellData.getValue().getTimestamp()));
        typeColumn.setCellValueFactory(cellData -> new SimpleStringProperty(cellData.getValue().getKey()));
        valueColumn.setCellValueFactory(cellData -> new SimpleObjectProperty<>(cellData.getValue().getValue()));
        alarmTypeColumn.setCellValueFactory(cellData -> new SimpleStringProperty(cellData.getValue().getAlarmType()));

        // Configuration de la nouvelle colonne pour la salle
        salleColumn.setCellValueFactory(cellData -> new SimpleStringProperty(getSalleFromMap(cellData.getValue())));

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

    //recupère la salle
    private String getSalleFromMap(Alarme alarme) {
        // Recherche de la salle à partir de la Map de alarmes
        for (Map.Entry<String, List<Alarme>> entry : alarmes.getAlarmes().entrySet()) {
            if (entry.getValue().contains(alarme)) {
                return entry.getKey(); // La clé de la Map est la salle
            }
        }
        return "Aucune salle"; // Retourne "Aucune salle" si non trouvé
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

    private void handleRecherche() {
        String salle = salleComboBox.getValue();
        String date = (datePicker.getValue() != null) ? datePicker.getValue().toString() : null;
        String selectedType = elementComboBox.getValue(); // Get the selected type from ComboBox
        Boolean isMax = selectedMax;
    
        Map<String, List<Alarme>> allAlarmes = alarmes.getAlarmes();
    
        List<Alarme> resultats = new ArrayList<>();
    
        for (Map.Entry<String, List<Alarme>> entry : allAlarmes.entrySet()) {
            if (salle != null && !salle.equals("ALL") && !salle.equals(entry.getKey())) {
                continue; // Filtrer par salle (si "Tous" n'est pas sélectionné)
            }
    
            for (Alarme alarme : entry.getValue()) {
                boolean correspond = true;
    
                // Filtrer par date
                if (date != null && !alarme.getTimestamp().startsWith(date)) {
                    correspond = false;
                }
    
                // Filtrer par type de donnée
                if (selectedType != null && !selectedType.equals("ALL") && !alarme.getKey().equals(selectedType.toLowerCase())) {
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
    
                if (correspond) {
                    resultats.add(alarme);
                }
            }
        }
    
        // Afficher les résultats
        displayResults(resultats);
    }
    
    
    
    
    
    
    // Méthode pour afficher les résultats dans le TableView
    private void displayResults(List<Alarme> alarmes) {
        resultTable.getItems().clear(); // Effacer les résultats précédents
    
        if (alarmes.isEmpty()) {
            // Si aucun résultat n'est trouvé, afficher un message dans le TableView
            showAlert("Aucun résultat", "Aucune alarme ne correspond à votre recherche.", Alert.AlertType.INFORMATION);
        } else {
            // Ajouter les alarmes au TableView
            resultTable.getItems().addAll(alarmes);
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
