package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import sae.App;
import sae.view.AppState;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;

public class SolarConfigController {

    private static final String CONFIG_FILE = "Iot/config.ini"; // Fichier de configuration
    private static final String PYTHON_SCRIPT = "Iot/main2.py"; // Script Python

    private Stage fenetrePrincipale;
    private App application;
    private Process pythonProcess; // Processus Python en cours
    private long pythonPID; // PID du processus Python en cours

    @FXML
    private Button butRetour;

    @FXML
    private Button butValider;

    @FXML
    private CheckBox cbCurrentPower;

    @FXML
    private CheckBox cbLastDayData;

    @FXML
    private CheckBox cbLastMonthData;

    @FXML
    private CheckBox cbLastYearData;

    @FXML
    private CheckBox cbLifeTimeData;

    @FXML
    private CheckBox cbLastUpdateTime;

    @FXML
    private Label lblInfo;

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
        // Étape 1 : Récupérer les CheckBoxes sélectionnées
        List<String> selections = getSelectedCheckBoxes();

        // Étape 2 : Mise à jour du fichier de configuration
        updateConfig(selections);

        // Étape 3 : Redémarrer le programme Python
        restartPythonScript();
    }

    private List<String> getSelectedCheckBoxes() {
        List<String> selectedCheckBoxes = new ArrayList<>();

        if (cbCurrentPower.isSelected()) selectedCheckBoxes.add("currentPower");
        if (cbLastDayData.isSelected()) selectedCheckBoxes.add("lastDayData");
        if (cbLastMonthData.isSelected()) selectedCheckBoxes.add("lastMonthData");
        if (cbLastYearData.isSelected()) selectedCheckBoxes.add("lastYearData");
        if (cbLifeTimeData.isSelected()) selectedCheckBoxes.add("lifeTimeData");
        if (cbLastUpdateTime.isSelected()) selectedCheckBoxes.add("lastUpdateTime");

        return selectedCheckBoxes;
    }

    public void updateConfig(List<String> donneesSolar) {
        try {
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));

            String newDonneesSolarLine = "donneesSolar=[" + donneesSolar.stream()
                    .map(attr -> "'" + attr + "'")
                    .collect(Collectors.joining(", ")) + "]";

            List<String> updatedLines = new ArrayList<>();
            boolean found = false;

            for (String line : lines) {
                if (line.startsWith("donneesSolar")) {
                    updatedLines.add(newDonneesSolarLine);
                    found = true;
                } else {
                    updatedLines.add(line);
                }
            }

            if (!found) {
                updatedLines.add(newDonneesSolarLine);
            }

            Files.write(Paths.get(CONFIG_FILE), updatedLines);
            lblInfo.setText("Modifications enregistrées avec succès !");
            lblInfo.setVisible(true);

            // Masquer l'information après 3 secondes
            new Thread(() -> {
                try {
                    Thread.sleep(3000);
                    lblInfo.setVisible(false);
                } catch (InterruptedException ignored) {}
            }).start();

        } catch (IOException e) {
            e.printStackTrace();
            lblInfo.setText("Erreur : Impossible de mettre à jour la configuration.");
        }
    }



    @FXML
    private void initialize() {
        try {
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            for (String line : lines) {
                if (line.startsWith("donneesSolar")) {
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    List<String> selectedItems = List.of(values.split(","))
                            .stream()
                            .map(v -> v.trim().replace("'", ""))
                            .collect(Collectors.toList());

                    cbCurrentPower.setSelected(selectedItems.contains("currentPower"));
                    cbLastDayData.setSelected(selectedItems.contains("lastDayData"));
                    cbLastMonthData.setSelected(selectedItems.contains("lastMonthData"));
                    cbLastYearData.setSelected(selectedItems.contains("lastYearData"));
                    cbLifeTimeData.setSelected(selectedItems.contains("lifeTimeData"));
                    cbLastUpdateTime.setSelected(selectedItems.contains("lastUpdateTime"));
                    break;
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement du fichier de configuration.");
        }
    }

    private void restartPythonScript() {
        stopPythonProcess();
        startPythonScript();
    }

    private boolean isProcessRunning(long pid) {
        try {
            Process process = new ProcessBuilder("cmd", "/c", "tasklist /FI \"PID eq " + pid + "\"").start();
            String output = new String(process.getInputStream().readAllBytes());
            return output.contains(String.valueOf(pid));
        } catch (IOException e) {
            e.printStackTrace();
            return false;
        }
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
}
