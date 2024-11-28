package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;

import java.io.IOException;

import org.eclipse.paho.client.mqttv3.MqttClient;
import org.eclipse.paho.client.mqttv3.MqttConnectOptions;
import org.eclipse.paho.client.mqttv3.MqttException;
import org.eclipse.paho.client.mqttv3.persist.MemoryPersistence;

public class MenuController  {

    private Stage fenetrePrincipale;
    private Process pythonProcess; // Variable pour stocker le processus Python

    @FXML
    private Button butSalles;
    @FXML
    private Button butSolar;
    @FXML
    private Button butAlarmes;
    @FXML
    private Button butTestCo;
    @FXML
    private Button butConfig;

    private App application;

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
        
        // Démarrer le processus Python lorsque l'application démarre
        startPythonScript();
        
        // Arrêter le processus Python à la fermeture de l'application
        this.fenetrePrincipale.setOnCloseRequest(event -> stopPythonScript());
    }

    @FXML
    private void actionBouttonSalles() {
        application.loadParametrageSalles();
    }

    @FXML
    private void actionBouttonSolar() {
        application.loadParametrageSolar();
    }

    @FXML
    private void actionBouttonConnexion() {
        try {
            MqttClient client = new MqttClient(
                "tcp://mqtt.iut-blagnac.fr:1883",
                MqttClient.generateClientId(),
                new MemoryPersistence());
            MqttConnectOptions options = new MqttConnectOptions();
            client.connect(options);
            if (client.isConnected()) {
                System.out.println("Connexion réussie");
            }
        } catch (MqttException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void actionBouttonConfig() {
        application.loadMenuConfig();
    }

    // Méthode pour démarrer le processus Python
    private void startPythonScript() {
        Thread pythonThread = new Thread(() -> {
            try {
                // Lancer le processus Python
                pythonProcess = new ProcessBuilder("python", "Iot/main2.py").start();
                System.out.println("Processus Python démarré.");
            } catch (IOException e) {
                e.printStackTrace();
                System.out.println("Erreur lors du lancement du script Python.");
            }
        });
        pythonThread.setDaemon(true); // Assurez-vous que le thread Python se termine à la fermeture de l'application
        pythonThread.start();
    }

    // Méthode pour arrêter le processus Python
    private void stopPythonScript() {
        if (pythonProcess != null && pythonProcess.isAlive()) {
            pythonProcess.destroy(); // Arrêter le processus Python
            System.out.println("Processus Python arrêté.");
        }
    }
}
