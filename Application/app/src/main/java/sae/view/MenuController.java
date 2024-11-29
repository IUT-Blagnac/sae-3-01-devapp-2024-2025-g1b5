package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;
import sae.view.AppState;

import java.io.IOException;

public class MenuController {

    private Stage fenetrePrincipale;
    private Process pythonProcess;

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
        // Test de connexion MQTT
    }

    @FXML
    private void actionBouttonConfig() {
        application.loadMenuConfig();
    }

    private void startPythonScript() {
        Thread pythonThread = new Thread(() -> {
            try {
                // Lancer le processus Python
                pythonProcess = new ProcessBuilder("python", "Iot/main2.py").start();
                long pid = pythonProcess.pid();

                // Sauvegarder le PID dans AppState
                AppState.setPythonPID(pid);
                System.out.println("Processus Python démarré avec PID : " + pid);
            } catch (IOException e) {
                e.printStackTrace();
                System.out.println("Erreur lors du lancement du script Python.");
            }
        });

        pythonThread.setDaemon(true); // S'assurer que le thread se termine avec l'application
        pythonThread.start();
    }

    private void stopPythonScript() {
        if (pythonProcess != null) {
            pythonProcess.destroy();
            System.out.println("Signal envoyé pour arrêter le processus Python.");

            try {
                boolean processTerminated = pythonProcess.waitFor(5, java.util.concurrent.TimeUnit.SECONDS);
                if (!processTerminated) {
                    pythonProcess.destroyForcibly();
                    System.out.println("Processus Python forcé à s'arrêter.");
                } else {
                    System.out.println("Processus Python arrêté proprement.");
                }
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }
}
