import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;

import java.io.*;
import java.nio.file.*;
import java.util.*;

public class ConfigEditorApp extends Application {

    private Map<String, String> salleData = new HashMap<>();

    @Override
    public void start(Stage primaryStage) {
        // Charger les données du fichier output.txt
        loadSalleData("output.txt");

        // Créer une liste déroulante pour sélectionner la salle
        ComboBox<String> salleComboBox = new ComboBox<>();
        salleComboBox.getItems().addAll(salleData.keySet());

        // Créer un bouton pour sauvegarder la sélection dans config.ini
        Button saveButton = new Button("Sauvegarder la sélection");

        Label infoLabel = new Label();

        saveButton.setOnAction(e -> {
            String selectedSalle = salleComboBox.getValue();
            if (selectedSalle != null) {
                updateConfigFile("config.ini", "selected_salle", selectedSalle);
                infoLabel.setText("Salle sélectionnée enregistrée : " + selectedSalle);
            } else {
                infoLabel.setText("Veuillez sélectionner une salle.");
            }
        });

        // Créer la mise en page
        VBox vbox = new VBox(10, salleComboBox, saveButton, infoLabel);
        Scene scene = new Scene(vbox, 400, 200);

        primaryStage.setTitle("Sélection de Salle");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    private void loadSalleData(String filePath) {
        try {
            String content = new String(Files.readAllBytes(Paths.get(filePath)));
            String[] parts = content.split("B201");
            if (parts.length > 1) {
                String[] salles = parts[1].split("]\\[");
                for (String salle : salles) {
                    salle = salle.replace("[", "").replace("]", "");
                    String[] values = salle.split(",");
                    if (values.length > 1) {
                        salleData.put(values[0], salle);
                    }
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private void updateConfigFile(String filePath, String key, String value) {
        try {
            Properties properties = new Properties();
            properties.load(new FileInputStream(filePath));
            properties.setProperty(key, value);
            properties.store(new FileOutputStream(filePath), null);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}
