package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Label;
import javafx.stage.Stage;
import sae.App;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.List;
import java.util.stream.Collectors;
import java.util.ArrayList;

public class SolarConfigController {

    private static final String CONFIG_FILE = "Iot/config.ini";
    private static final String PYTHON_SCRIPT = "python Iot/main2.py"; // Commande pour exécuter le script Python

    private Stage fenetrePrincipale;
    private App application;

    @FXML
    Button butRetour;

    @FXML
    Button butValider;

    @FXML
    CheckBox cbCurrentPower;

    @FXML
    CheckBox cbLastDayData;

    @FXML
    CheckBox cbLastMonthData;

    @FXML
    CheckBox cbLastYearData;

    @FXML
    CheckBox cbLifeTimeData;

    @FXML
    CheckBox cbLastUpdateTime;

    @FXML
    private Label lblInfo;

    private Process pythonProcess; // Processus Python en cours

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

        // Étape 3 : Arrêter et relancer le programme Python
        restartPythonScript();
    }

    private List<String> getSelectedCheckBoxes() {
        // Utiliser une liste dynamique pour éviter les problèmes avec des valeurs nulles
        List<String> selectedCheckBoxes = new ArrayList<>();

        if (cbCurrentPower != null && cbCurrentPower.isSelected()) {
            selectedCheckBoxes.add("currentPower");
        }
        if (cbLastDayData != null && cbLastDayData.isSelected()) {
            selectedCheckBoxes.add("lastDayData");
        }
        if (cbLastMonthData != null && cbLastMonthData.isSelected()) {
            selectedCheckBoxes.add("lastMonthData");
        }
        if (cbLastYearData != null && cbLastYearData.isSelected()) {
            selectedCheckBoxes.add("lastYearData");
        }
        if (cbLifeTimeData != null && cbLifeTimeData.isSelected()) {
            selectedCheckBoxes.add("lifeTimeData");
        }
        if (cbLastUpdateTime != null && cbLastUpdateTime.isSelected()) {
            selectedCheckBoxes.add("lastUpdateTime");
        }

        return selectedCheckBoxes;
    }

    public void updateConfig(List<String> donneesSolar) {
        try {
            // Charger toutes les lignes du fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));

            // Créer la nouvelle ligne pour donneesSolar
            String newDonneesSolarLine = "donneesSolar=[" + donneesSolar.stream()
                    .map(attr -> "'" + attr + "'")
                    .collect(Collectors.joining(", ")) + "]";

            // Mettre à jour ou ajouter la ligne donneesSolar
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
                updatedLines.add(newDonneesSolarLine); // Ajouter si absent
            }

            // Écrire les nouvelles lignes dans le fichier
            Files.write(Paths.get(CONFIG_FILE), updatedLines);
            System.out.println("Fichier de configuration mis à jour avec succès.");
            lblInfo.setText("Modifications enregistrées avec succès !");
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
            e.printStackTrace();
            System.out.println("Erreur lors de la mise à jour du fichier de configuration : " + e.getMessage());
        }
    }

    private void restartPythonScript() {
        // Si le processus Python est déjà en cours, on l'arrête
        if (pythonProcess != null && pythonProcess.isAlive()) {
            pythonProcess.destroy(); // Arrêter le processus en cours
            System.out.println("Processus Python arrêté.");
        }

        // Lancer le script Python dans un thread séparé
        Thread pythonThread = new Thread(() -> {
            try {
                pythonProcess = new ProcessBuilder("python", "Iot/main2.py").start(); // Démarrer le processus Python
                System.out.println("Processus Python relancé.");
            } catch (IOException e) {
                e.printStackTrace();
                System.out.println("Erreur lors du lancement du script Python.");
            }
        });

        pythonThread.setDaemon(true); // Marquer le thread comme démon pour qu'il se termine lorsque l'application ferme
        pythonThread.start();
    }

    @FXML
    private void initialize() {
        // Charger les données de configuration
        try {
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            for (String line : lines) {
                if (line.startsWith("donneesSolar")) {
                    // Extraire la liste des valeurs entre crochets
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    List<String> selectedItems = List.of(values.split(","))
                            .stream()
                            .map(v -> v.trim().replace("'", "")) // Supprimer les espaces et les apostrophes
                            .collect(Collectors.toList());

                    // Cocher les CheckBoxes correspondantes
                    if (cbCurrentPower != null) {
                        cbCurrentPower.setSelected(selectedItems.contains("currentPower"));
                    }
                    if (cbLastDayData != null) {
                        cbLastDayData.setSelected(selectedItems.contains("lastDayData"));
                    }
                    if (cbLastMonthData != null) {
                        cbLastMonthData.setSelected(selectedItems.contains("lastMonthData"));
                    }
                    if (cbLastYearData != null) {
                        cbLastYearData.setSelected(selectedItems.contains("lastYearData"));
                    }
                    if (cbLifeTimeData != null) {
                        cbLifeTimeData.setSelected(selectedItems.contains("lifeTimeData"));
                    }
                    if (cbLastUpdateTime != null) {
                        cbLastUpdateTime.setSelected(selectedItems.contains("lastUpdateTime"));
                    }
                    break;
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement du fichier de configuration : " + e.getMessage());
        }
    }
}
