package sae.appli;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

public class Alarmes {
    private Map<String, List<Alarme>> alarmes;

    // Constructeur
    public Alarmes() {
        this.alarmes = new HashMap<>();
    }

    // Charger les données depuis un fichier JSON
    public void loadFromJson(String filePath) throws IOException {
        ObjectMapper objectMapper = new ObjectMapper();
        JsonNode rootNode = objectMapper.readTree(new File(filePath));

        rootNode.fields().forEachRemaining(entry -> {
            String location = entry.getKey();
            JsonNode alarmsNode = entry.getValue();
            List<Alarme> alarmsList = new ArrayList<>();

            alarmsNode.fields().forEachRemaining(alarmEntry -> {
                JsonNode alarmData = alarmEntry.getValue();
                Alarme alarme = new Alarme(
                        alarmData.get("key").asText(),
                        alarmData.get("value").asDouble(),
                        alarmData.get("alarm_type").asText(),
                        alarmData.get("timestamp").asText()
                );
                alarmsList.add(alarme);
            });

            this.alarmes.put(location, alarmsList);
        });
    }

    // Obtenir toutes les alarmes
    public Map<String, List<Alarme>> getAlarmes() {
        return alarmes;
    }

    // Obtenir les alarmes d'un lieu spécifique
    public List<Alarme> getAlarmesByLocation(String location) {
        return alarmes.getOrDefault(location, new ArrayList<>());
    }

    @Override
    public String toString() {
        return "Alarmes{" +
                "alarmes=" + alarmes +
                '}';
    }
}
