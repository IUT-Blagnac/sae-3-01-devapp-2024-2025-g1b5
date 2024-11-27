package sae.appli;

import org.eclipse.paho.client.mqttv3.*;
import org.eclipse.paho.client.mqttv3.persist.MemoryPersistence;

import java.io.IOException;
import java.nio.file.*;

public class MqttAlarmListener {
    
    private static final String MQTT_SERVER = "tcp://mqtt.iut-blagnac.fr:1883";
    private static final String MQTT_TOPIC = "alarms"; // Topic à souscrire pour recevoir des alertes
    private static final String FLAG_FILE = "trigger.flag"; // Fichier trigger.flag
    
    private MqttClient client;

    public void start() {
        try {
            // Option pour ne pas créer de fichiers temporaires
            System.setProperty("paho.mqtt.persistDir", ""); // Empêche la création de fichiers temporaires

            // Créer un client MQTT sans persistance
            MqttClientPersistence persistence = new MemoryPersistence(); // Utiliser la mémoire pour la persistance (aucun fichier)
            client = new MqttClient(MQTT_SERVER, MqttClient.generateClientId(), persistence);

            MqttConnectOptions options = new MqttConnectOptions();
            options.setCleanSession(true); // Session propre
            client.connect(options);
            System.out.println("MQTT Client connecté.");
            
            // Souscrire au topic des alarmes
            client.subscribe(MQTT_TOPIC, (topic, msg) -> {
                // Lorsque le message MQTT est reçu, mettre à jour le fichier trigger.flag
                String message = new String(msg.getPayload());
                updateTriggerFlagFile(message);
            });
            
            System.out.println("Abonné au topic: " + MQTT_TOPIC);
        } catch (MqttException e) {
            e.printStackTrace();
        }
    }

    private void updateTriggerFlagFile(String alarmMessage) {
        try {
            // Écrire le message reçu dans le fichier trigger.flag
            Files.write(Paths.get(FLAG_FILE), alarmMessage.getBytes(), StandardOpenOption.CREATE, StandardOpenOption.TRUNCATE_EXISTING);
            System.out.println("trigger.flag mis à jour.");
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
