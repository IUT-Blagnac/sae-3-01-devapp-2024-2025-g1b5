package sae.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import java.io.BufferedReader;
import java.io.FileReader;

public class AlarmPopUpController {

    @FXML
    public void showAlarmPopUp() {
        // Chemin du fichier trigger.flag
        String filePath = "C:\\\\Users\\\\Etudiant\\\\Documents\\\\GitHub\\\\sae-3-01-devapp-2024-2025-g1b5\\\\Application\\\\app\\\\src\\\\main\\\\resources\\\\sae\\\\iot\\\\trigger.flag"; // Assurez-vous que le fichier se trouve à cet emplacement
        String alarmMessage = readAlarmFromFile(filePath);

        if (alarmMessage != null && !alarmMessage.isEmpty()) {
            // Exécuter l'affichage du pop-up sur le JavaFX Application Thread
            Platform.runLater(() -> {
                Alert alert = new Alert(AlertType.WARNING);
                alert.setTitle("Alarme Déclenchée");
                alert.setHeaderText("Une alarme a été déclenchée");
                alert.setContentText(alarmMessage);

                alert.showAndWait();
            });
        }
    }

    private String readAlarmFromFile(String filePath) {
        // Lire le fichier trigger.flag et parser le JSON
        JSONParser parser = new JSONParser();
        try (BufferedReader reader = new BufferedReader(new FileReader(filePath))) {
            String line;
            StringBuilder jsonContent = new StringBuilder();
            while ((line = reader.readLine()) != null) {
                jsonContent.append(line);
            }

            // Parse le contenu JSON
            JSONObject jsonObject = (JSONObject) parser.parse(jsonContent.toString());

            // Extraire les informations de l'alarme
            String room = (String) jsonObject.get("room");
            JSONObject alarm = (JSONObject) jsonObject.get("alarm");
            String key = (String) alarm.get("key");

            // Gestion des valeurs numériques et de chaînes pour 'value'
            Object valueObj = alarm.get("value");
            String value;
            if (valueObj instanceof Number) {
                value = String.valueOf(valueObj); // Si c'est un nombre, on le convertit en chaîne
            } else {
                value = (String) valueObj; // Sinon, on le prend comme une chaîne
            }

            String alarmType = (String) alarm.get("alarm_type");
            String timestamp = (String) alarm.get("timestamp");

            // Formater le message d'alarme
            return String.format("Alarme dans la salle : %s\nDonnée : %s\nValeur : %s\nType : %s\nDate : %s",
                    room, key, value, alarmType, timestamp);

        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }
}
