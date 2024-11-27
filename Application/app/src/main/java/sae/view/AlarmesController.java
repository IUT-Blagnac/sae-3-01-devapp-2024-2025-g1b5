package sae.view;

import sae.appli.Salle;
import sae.appli.Alarme;
import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.ComboBox;
import javafx.scene.control.DatePicker;
import javafx.scene.control.TableView;
import javafx.scene.control.TableColumn;
import javafx.scene.control.cell.PropertyValueFactory;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

public class AlarmesController {

    // Déclaration des composants de l'interface
    @FXML
    private ComboBox<String> comboSalle;
    @FXML
    private ComboBox<String> comboTypeAlarme;
    @FXML
    private DatePicker datePicker;
    @FXML
    private TableView<Alarme> tableViewAlarmes;
    @FXML
    private TableColumn<Alarme, String> colKey;
    @FXML
    private TableColumn<Alarme, Double> colValue;
    @FXML
    private TableColumn<Alarme, String> colAlarmType;
    @FXML
    private TableColumn<Alarme, String> colTimestamp;

    // Liste pour stocker les salles et les alarmes
    private List<Salle> salles;

    // Méthode d'initialisation
    @FXML
    public void initialize() {
        salles = new ArrayList<>();
        loadAlarmeData(); // Charger les alarmes depuis le fichier JSON

        // Initialisation des colonnes de la TableView
        colKey.setCellValueFactory(new PropertyValueFactory<>("key"));
        colValue.setCellValueFactory(new PropertyValueFactory<>("value"));
        colAlarmType.setCellValueFactory(new PropertyValueFactory<>("alarm_type"));
        colTimestamp.setCellValueFactory(new PropertyValueFactory<>("timestamp"));

        // Remplir le ComboBox des salles et des types d'alarmes
        ObservableList<String> salleNames = FXCollections.observableArrayList();
        for (Salle salle : salles) {
            salleNames.add(salle.toString());
        }
        comboSalle.setItems(salleNames);
        
        ObservableList<String> alarmTypes = FXCollections.observableArrayList("temperature", "co2", "tvoc", "activity", "pressure", "humidity", "illumination", "infrared");
        comboTypeAlarme.setItems(alarmTypes);
    }

    // Méthode pour charger les alarmes depuis un fichier JSON
    private void loadAlarmeData() {
        ObjectMapper objectMapper = new ObjectMapper();
        try {
            // Lire le fichier JSON des alarmes
            File jsonFile = new File("path/to/alarms.json");
            JsonNode rootNode = objectMapper.readTree(jsonFile);

            // Parcourir chaque salle dans le fichier JSON
            for (Iterator<String> it = rootNode.fieldNames(); it.hasNext(); ) {
                String salleName = it.next();
                JsonNode salleNode = rootNode.get(salleName);

                Salle salle = new Salle(salleName);
                // Pour chaque alarme dans cette salle
                for (JsonNode alarmNode : salleNode) {
                    String key = alarmNode.get("key").asText();
                    double value = alarmNode.get("value").asDouble();
                    String alarmType = alarmNode.get("alarm_type").asText();
                    String timestamp = alarmNode.get("timestamp").asText();

                    // Créer un objet Alarme et l'ajouter à la salle
                    Alarme alarme = new Alarme(key, value, alarmType, timestamp);
                    salle.ajouterAlarme(alarme);
                }

                // Ajouter la salle avec ses alarmes à la liste des salles
                salles.add(salle);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    // Méthode pour afficher les alarmes en fonction des critères sélectionnés
    @FXML
    private void afficherAlarmes() {
        // Récupérer les critères de filtrage
        String selectedSalle = comboSalle.getValue();
        String selectedType = comboTypeAlarme.getValue();
        String selectedDate = datePicker.getValue() != null ? datePicker.getValue().toString() : "";

        // Filtrer les alarmes en fonction des critères
        List<Alarme> filteredAlarmes = new ArrayList<>();
        for (Salle salle : salles) {
            if (salle.toString().equals(selectedSalle) || selectedSalle == null) {
                for (Alarme alarme : salle.getAlarmes()) {
                    if ((selectedType == null || alarme.getKey().equals(selectedType)) &&
                        (selectedDate.isEmpty() || alarme.getTimestamp().contains(selectedDate))) {
                        filteredAlarmes.add(alarme);
                    }
                }
            }
        }

        // Afficher les alarmes filtrées dans la table
        ObservableList<Alarme> alarmesObservableList = FXCollections.observableArrayList(filteredAlarmes);
        tableViewAlarmes.setItems(alarmesObservableList);
    }
}
