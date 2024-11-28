package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ComboBox;
import javafx.scene.control.DatePicker;
import javafx.scene.layout.VBox;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import sae.App;
import sae.appli.TypeDonnee;
import sae.appli.Alarme;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.ObjectMapper;

public class AlarmesController {

    // Déclarations des composants FXML
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

    private List<String> salles = new ArrayList<>(); // Liste dynamique de salles
    private Boolean selectedMax = null; // Etat des boutons "Max" et "Min", initialisé à null (aucun sélectionné)

    private Alarme alarmes;

    // Charger les alarmes depuis le fichier JSON
    private void loadAlarmsFromFile(String filePath) {
        ObjectMapper objectMapper = new ObjectMapper();

        try {
            // Lire le fichier JSON et mapper les données dans l'objet Alarme
            alarmes = objectMapper.readValue(new File(filePath), Alarme.class);
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement des données JSON.");
        }
    }

    // Méthode d'initialisation
    @FXML
    public void initialize() {
        String filePath = "C:\\\\Users\\\\Etudiant\\\\Documents\\\\GitHub\\\\sae-3-01-devapp-2024-2025-g1b5\\\\Application\\\\app\\\\src\\\\main\\\\resources\\\\sae\\\\iot\\\\trigger.flag";
        loadAlarmsFromFile(filePath);

        // Initialisation des éléments de la ComboBox pour l'élément recherché (TypeDonnee)
        elementComboBox.getItems().addAll(TypeDonnee.values()); // Ajouter toutes les valeurs de l'énumération TypeDonnee

        // Initialisation dynamique des salles dans le ComboBox
        loadSalles();  // Appel à une méthode pour charger les salles dynamiquement
        salleComboBox.getItems().addAll(salles);  // Ajout des salles au ComboBox

        // Gérer l'action du bouton "Max"
        maxButton.setOnAction(e -> handleMax());

        // Gérer l'action du bouton "Min"
        minButton.setOnAction(e -> handleMin());

        // Gérer l'action du bouton "Recherche"
        rechercheButton.setOnAction(e -> handleRecherche());

        // Gérer l'action du bouton "Retour"
        retourButton.setOnAction(e -> handleRetour());
    }

    // Méthode pour charger les salles dynamiquement
    private void loadSalles() {
        // Liste des salles statiques (cela peut être remplacé par une récupération dynamique depuis une base de données, fichier, etc.)
        String[] sls = {
            "B001", "E004", "E106", "Foyer-personnels", "Local-velo", "B202", "C004",
            "B201", "C001", "B109", "Salle-conseil", "B002", "B105", "C101",
            "Foyer-etudiants-entrée", "B234", "B111", "B113", "E006", "E104",
            "E209", "E003", "B217", "C002", "B112", "E001", "B108", "C102",
            "E007", "B203", "E208", "amphi1", "E210", "B103", "E101", "E207",
            "E100", "C006", "hall-amphi", "E102", "hall-entrée-principale",
            "B110", "E103"
        };

        // Ajout de chaque salle à la liste
        for (String salle : sls) {
            salles.add(salle);
        }
    }

    // Méthode pour gérer la sélection "Max"
    private void handleMax() {
        if (selectedMax == null || selectedMax == false) {
            // Si aucun bouton n'est sélectionné ou si "Min" était sélectionné, sélectionner "Max"
            selectedMax = true;
            updateButtonStyles();
            System.out.println("Max sélectionné");
        } else {
            // Si "Max" est déjà sélectionné, désélectionner
            selectedMax = null;
            updateButtonStyles();
            System.out.println("Aucun sélectionné");
        }
    }

    // Méthode pour gérer la sélection "Min"
    private void handleMin() {
        if (selectedMax == null || selectedMax == true) {
            // Si aucun bouton n'est sélectionné ou si "Max" était sélectionné, sélectionner "Min"
            selectedMax = false;
            updateButtonStyles();
            System.out.println("Min sélectionné");
        } else {
            // Si "Min" est déjà sélectionné, désélectionner
            selectedMax = null;
            updateButtonStyles();
            System.out.println("Aucun sélectionné");
        }
    }

    // Méthode pour mettre à jour les styles des boutons
    private void updateButtonStyles() {
        // Vérifier que selectedMax n'est pas null avant de l'utiliser
        if (selectedMax == null) {
            // Aucun bouton sélectionné : appliquer le style par défaut
            maxButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
            minButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
        } else if (selectedMax == true) {
            // Si "Max" est sélectionné
            maxButton.setStyle("-fx-background-color: green; -fx-text-fill: white;");
            minButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
        } else {
            // Si "Min" est sélectionné
            minButton.setStyle("-fx-background-color: red; -fx-text-fill: white;");
            maxButton.setStyle("-fx-background-color: lightgray; -fx-text-fill: black;");
        }
    }

    // Méthode pour effectuer la recherche des alarmes en fonction des critères sélectionnés
    private void handleRecherche() {
        if (alarmes == null) {
            System.out.println("Les alarmes n'ont pas été chargées correctement.");
            return;
        }
    
        // Récupérer les critères de recherche
        String selectedSalle = salleComboBox.getValue();
        TypeDonnee selectedElement = elementComboBox.getValue();
        String selectedDate = datePicker.getValue() != null ? datePicker.getValue().toString() : null;
    
        if (selectedSalle == null || selectedElement == null) {
            System.out.println("Veuillez sélectionner une salle et un élément.");
            return;
        }
    
        // Créer une liste pour les alarmes filtrées
        List<Alarme.AlarmDetails> filteredAlarms = new ArrayList<>();
        Map<String, Map<String, Alarme.AlarmDetails>> alarmesData = alarmes.getAlarmes();
    
        boolean isAnyFilterSelected = false;
    
        // Parcours des alarmes par salle
        for (Map.Entry<String, Map<String, Alarme.AlarmDetails>> entry : alarmesData.entrySet()) {
            // Filtrer par salle
            if (selectedSalle != null && entry.getKey().equals(selectedSalle) || selectedSalle == null) {
                // Parcours des alarmes dans chaque salle
                for (Alarme.AlarmDetails alarm : entry.getValue().values()) {
                    // Filtrer par élément
                    boolean matchesElement = (selectedElement == null || alarm.getKey().equals(selectedElement.name()));
    
                    // Filtrer par type d'alarme (MAX ou MIN)
                    boolean matchesMaxMin = (selectedMax == null) || 
                                             (selectedMax && alarm.getAlarm_type().equals("MAX")) ||
                                             (!selectedMax && alarm.getAlarm_type().equals("MIN"));
    
                    // Filtrer par date
                    boolean matchesDate = (selectedDate == null || alarm.getTimestamp().startsWith(selectedDate));
    
                    // Si l'alarme correspond à tous les critères, l'ajouter à la liste des résultats filtrés
                    if (matchesElement && matchesMaxMin && matchesDate) {
                        filteredAlarms.add(alarm);
                        isAnyFilterSelected = true;
                    }
                }
            }
        }
    
        // Afficher les résultats filtrés
        displayResults(filteredAlarms, isAnyFilterSelected);
    }
    
    
    // Méthode pour afficher les résultats dans la VBox
    private void displayResults(List<Alarme.AlarmDetails> filteredAlarms, boolean isAnyFilterSelected) {
        // Effacer les résultats précédents
        resultVBox.getChildren().clear();
    
        if (filteredAlarms.isEmpty()) {
            // Si aucune alarme ne correspond aux critères, afficher un message
            if (isAnyFilterSelected) {
                resultVBox.getChildren().add(new Label("Aucune alarme trouvée pour les critères sélectionnés"));
            } else {
                resultVBox.getChildren().add(new Label("Veuillez sélectionner des critères de recherche"));
            }
        } else {
            // Afficher les alarmes filtrées
            for (Alarme.AlarmDetails alarm : filteredAlarms) {
                resultVBox.getChildren().add(new Label("Alarme : " + alarm.getKey() +
                                                      " - Valeur : " + alarm.getValue() +
                                                      " - Type : " + alarm.getAlarm_type() +
                                                      " - Timestamp : " + alarm.getTimestamp()));
            }
        }
    }
    

    // Ajouter une méthode pour gérer le retour (fermeture de la fenêtre ou retour au menu)
    @FXML
    private void handleRetour() {
        // Code pour retourner à la page précédente ou fermer la fenêtre
        application.loadMenu();
    }

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
        //this.fenetrePrincipale.setOnCloseRequest(event -> actionQuitter());
    }
}
