package sae.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Alert.AlertType;
import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import java.io.BufferedReader;
import java.io.FileReader;
import java.nio.file.Files;
import java.nio.file.Paths;

public class AlarmPopUpController {

    @FXML
    public void showAlarmPopUp() {
        // Chemin du fichier trigger.flag
        String filePath = "../../Iot/trigger.flag"; // Assurez-vous que le fichier se trouve à cet emplacement
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
        JSONParser parser = new JSONParser();
        try (BufferedReader reader = new BufferedReader(new FileReader(filePath))) {
            // Vérifier si le fichier n'est pas vide
            if (Files.size(Paths.get(filePath)) == 0) {
                System.out.println("Le fichier est vide, aucune alarme à lire.");
                return null;
            }

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

            Object valueObj = alarm.get("value");
            String value = valueObj instanceof Number ? String.valueOf(valueObj) : (String) valueObj;

            String alarmType = (String) alarm.get("alarm_type");
            String timestamp = (String) jsonObject.get("timestamp");

            return String.format(
                "Alarme dans la salle : %s\nDonnée : %s\nValeur : %s\nType : %s\nDate : %s",
                room, key, value, alarmType, timestamp);

        } catch (Exception e) {
            e.printStackTrace();
            return null; // En cas d'erreur, retourne null
        }
    }

}
