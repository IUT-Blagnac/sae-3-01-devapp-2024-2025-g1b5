package sae.view;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.List;
import java.util.stream.Collectors;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.Label;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import sae.App;
import sae.appli.AppState;

public class FreqConfigController {

    private static final String PYTHON_SCRIPT = "../Iot/main2.py"; // Script Python
    private static final String CONFIG_FILE = "../Iot/config.ini";

    @SuppressWarnings("unused")
    private Stage fenetrePrincipale;
    private App application;

    @FXML
    private Button butRetour;

    @FXML
    private Button butValider;

    @FXML
    private TextField freqInput; // Champ pour saisir uniquement des nombres

    @FXML
    private Label lblInfo; // Label pour afficher des messages

    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }

    @FXML
    private void actionRetour() {
        application.loadMenuConfig();
    }

    @FXML
    private void actionValid() {
        String freqValue = freqInput.getText().trim();

        if (freqValue.matches("\\d+")) { // Vérifie que la valeur est un nombre
            updateConfig(freqValue);
            restartPythonScript();
        } else {
            lblInfo.setText("Veuillez entrer un nombre valide !");
            lblInfo.setVisible(true);
        }
    }

    private void updateConfig(String freqValue) {
        try {
            // Charger toutes les lignes du fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));

            // Créer la nouvelle ligne pour la fréquence
            String newFreqLine = "frequence=" + freqValue;

            // Mettre à jour ou ajouter la ligne fréquence
            List<String> updatedLines = lines.stream()
                .map(line -> line.startsWith("frequence") ? newFreqLine : line)
                .collect(Collectors.toList());

            if (!updatedLines.contains(newFreqLine)) {
                updatedLines.add(newFreqLine); // Ajouter si absent
            }

            // Écrire les nouvelles lignes dans le fichier
            Files.write(Paths.get(CONFIG_FILE), updatedLines);
            lblInfo.setText("Configuration mise à jour avec succès !");
            lblInfo.setVisible(true);

            // Masquer le message après 3 secondes
            new Thread(() -> {
                try {
                    Thread.sleep(3000);
                    lblInfo.setVisible(false);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
            }).start();

        } catch (IOException e) {
            lblInfo.setText("Erreur lors de la mise à jour de la configuration !");
            lblInfo.setVisible(true);
            e.printStackTrace();
        }
    }

    private void restartPythonScript() {
        stopPythonProcess();
        startPythonScript();
    }

    private void stopPythonProcess() {
        long pid = AppState.getPythonPID();
        if (pid > 0) {
            try {
                // Détection du système d'exploitation
                String os = System.getProperty("os.name").toLowerCase();
    
                if (os.contains("win")) {
                    // Commande Windows : arrêter le processus avec "taskkill"
                    Process process = new ProcessBuilder("cmd", "/c", "taskkill /PID " + pid + " /F").start();
                    int exitCode = process.waitFor();
                    if (exitCode == 0) {
                        System.out.println("Le processus Python avec PID : " + pid + " a été arrêté sous Windows.");
                    } else {
                        System.out.println("Échec de l'arrêt du processus Python avec PID : " + pid + " sous Windows.");
                    }
                } else if (os.contains("nix") || os.contains("nux") || os.contains("mac")) {
                    // Commande Linux/Mac : arrêter le processus avec "kill -9"
                    Process process = new ProcessBuilder("kill", "-9", String.valueOf(pid)).start();
                    int exitCode = process.waitFor();
                    if (exitCode == 0) {
                        System.out.println("Le processus Python avec PID : " + pid + " a été arrêté sous Linux/Mac.");
                    } else {
                        System.out.println("Échec de l'arrêt du processus Python avec PID : " + pid + " sous Linux/Mac.");
                    }
                }
                // Réinitialiser le PID après arrêt
                AppState.setPythonPID(-1);
            } catch (IOException | InterruptedException e) {
                e.printStackTrace();
                System.out.println("Erreur lors de l'arrêt du processus Python.");
            }
        }
    }

    private void startPythonScript() {
        try {
            Process pythonProcess = new ProcessBuilder("python", PYTHON_SCRIPT).start();
            AppState.setPythonPID(pythonProcess.pid());
            System.out.println("Nouveau processus Python démarré avec PID : " + pythonProcess.pid());
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    @FXML
    private void initialize() {
        try {
            // Charger la fréquence actuelle depuis le fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            for (String line : lines) {
                if (line.startsWith("frequence")) {
                    String currentFreq = line.split("=")[1].trim();
                    freqInput.setText(currentFreq); // Pré-remplir le champ avec la fréquence actuelle
                    break;
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
