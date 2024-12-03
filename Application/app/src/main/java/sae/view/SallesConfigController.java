package sae.view;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.List;
import java.util.stream.Collectors;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.Label;
import javafx.scene.control.ScrollPane;
import javafx.scene.layout.VBox;
import javafx.stage.Stage;
import sae.App;
import sae.appli.TypeDonnee;
import sae.view.AppState;

import java.util.ArrayList;

public class SallesConfigController {

    private static final String PYTHON_SCRIPT = "Iot/main2.py"; // Script Python
    private static final String CONFIG_FILE = "Iot/config.ini";
    private Process pythonProcess; // Processus Python en cours
    private long pythonPID; // PID du processus Python en cours


    @SuppressWarnings("unused")
    private Stage fenetrePrincipale;
    private App application;

    @FXML
    Button butRetour;

    @FXML
    Button butValider;

    @FXML
    ScrollPane scrollPane;
    
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
        restartPythonScript();
    }

    private List<String> getSelectedCheckBoxes() {
        // Utiliser une liste dynamique pour éviter les problèmes avec des valeurs nulles
        List<String> selectedCheckBoxes = new ArrayList<>();

        // Parcourir tous les enfants du conteneur de boutons
        for (int i = 0; i < ((VBox) scrollPane.getContent()).getChildren().size(); i++) {
            CheckBox cb = (CheckBox) ((VBox) scrollPane.getContent()).getChildren().get(i);
            if (cb.isSelected()) {
                selectedCheckBoxes.add(cb.getText().toLowerCase());
            }
        }

        return selectedCheckBoxes;
    }

    public void updateConfig(List<String> donneesSalles) {
        try {
            // Créer les nouvelles listes pour les seuils
            List<Integer> seuilMinList = new ArrayList<>();
            List<Integer> seuilMaxList = new ArrayList<>();
    
            for (String salle : donneesSalles) {
                if (TypeDonnee.containsType(salle)) {
                    int[] seuils = TypeDonnee.getSeuilsByNom(salle);
                    seuilMinList.add(seuils[0]);
                    seuilMaxList.add(seuils[1]);
                }
            }
    
            // Construire les nouvelles lignes pour la configuration
            String newDonneesSallesLine = "donneesSalles=[" + donneesSalles.stream()
                    .map(attr -> "'" + attr + "'")
                    .collect(Collectors.joining(", ")) + "]";
            String newSeuilMinLine = "seuil_min=[" + seuilMinList.stream()
                    .map(String::valueOf)
                    .collect(Collectors.joining(", ")) + "]";
            String newSeuilMaxLine = "seuil_max=[" + seuilMaxList.stream()
                    .map(String::valueOf)
                    .collect(Collectors.joining(", ")) + "]";
    
            // Charger les lignes existantes
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            List<String> updatedLines = new ArrayList<>();
            boolean donneesSallesFound = false;
            boolean seuilMinFound = false;
            boolean seuilMaxFound = false;
    
            // Mettre à jour ou ajouter les lignes correspondantes
            for (String line : lines) {
                if (line.startsWith("donneesSalles")) {
                    updatedLines.add(newDonneesSallesLine);
                    donneesSallesFound = true;
                } else if (line.startsWith("seuil_min")) {
                    updatedLines.add(newSeuilMinLine);
                    seuilMinFound = true;
                } else if (line.startsWith("seuil_max")) {
                    updatedLines.add(newSeuilMaxLine);
                    seuilMaxFound = true;
                } else {
                    updatedLines.add(line);
                }
            }
    
            // Ajouter les lignes manquantes si nécessaire
            if (!donneesSallesFound) updatedLines.add(newDonneesSallesLine);
            if (!seuilMinFound) updatedLines.add(newSeuilMinLine);
            if (!seuilMaxFound) updatedLines.add(newSeuilMaxLine);
    
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
    

    @FXML
    private void initialize() {

        //charger les boutons de configuration
        VBox buttonContainer = new VBox(10); 
        TypeDonnee[] donnees = TypeDonnee.values();

        scrollPane.setFitToWidth(true); 
        scrollPane.setPannable(true);
        scrollPane.setContent(buttonContainer);


        
        // Charger les données de configuration
        try {

            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            for (String line : lines) {
                if (line.startsWith("donneesSalles")) {
                    // Extraire la liste des valeurs entre crochets
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    List<String> selectedItems = List.of(values.split(",")).stream().map(v -> v.trim().replace("'", "")).collect(Collectors.toList());
                    for (TypeDonnee donnee : donnees) {
                        CheckBox cb = new CheckBox(donnee.toString());
                        cb.setUserData(donnee);
                        
                        if (selectedItems.contains(donnee.toString().toLowerCase())) {
                            cb.setSelected(true);
                        }
                        buttonContainer.getChildren().add(cb);
                    }
                    return;
                }
            }
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement du fichier de configuration : " + e.getMessage());
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
