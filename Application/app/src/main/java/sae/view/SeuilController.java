package sae.view;

import javafx.collections.ObservableList;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.CheckMenuItem;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import sae.App;  // Assure-toi d'importer App correctement
import sae.appli.TypeDonnee;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
import java.util.List;
import java.util.stream.Collectors;
import sae.appli.AppState;

public class SeuilController {

    @FXML
    private MenuButton choixTypeDonnees;

    @FXML
    private TextField minValueField;

    @FXML
    private TextField maxValueField;

    @FXML
    private Button butRetour;

    @FXML
    private Button butValider;

    private Stage stage;
    private App application;  // Référence à l'application
    private List<String> choices = new ArrayList<>();  // Liste des choix sélectionnés

    private static final String PYTHON_SCRIPT = "Iot/main2.py"; // Script Python

    // Méthode pour configurer les données du controller
    public void setDatas(Stage stage, App application) {
        this.stage = stage;
        this.application = application;  // Initialisation de la variable application
    }


    // Méthode pour initialiser la vue, appelant loadMenuDeroulantDonnees
    @FXML
    private void initialize() {
        loadMenuDeroulantDonnees();  // Appelle la méthode de chargement des données
    }



    // Méthode pour gérer la sélection unique dans le menu déroulant
    private void handleSelectionUnique(CheckMenuItem selected) {
        // Désélectionner tous les autres CheckMenuItem
        ObservableList<MenuItem> items = choixTypeDonnees.getItems();
        for (MenuItem item : items) {
            if (item instanceof CheckMenuItem) {
                CheckMenuItem checkMenuItem = (CheckMenuItem) item;
                if (!checkMenuItem.equals(selected)) {
                    checkMenuItem.setSelected(false); // Désélectionner les autres éléments
                }
            }
        }
    }
    


    // Méthode pour charger les données dans le MenuButton
    public void loadMenuDeroulantDonnees() {
        try {
            // Lecture du fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get("Iot/config.ini"));
            List<String> donneesSalles = new ArrayList<>();
    
            // Extraction des données de la section [data]
            for (String line : lines) {
                if (line.startsWith("donneesSalles")) {
                    // Extraire les valeurs entre crochets
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    donneesSalles = List.of(values.split(","))
                            .stream().map(v -> v.trim().replace("'", ""))
                            .collect(Collectors.toList());
    
                    // Ajouter les CheckMenuItem au MenuButton
                    for (String donnee : donneesSalles) {
                        CheckMenuItem cb = new CheckMenuItem(donnee);
                        cb.setUserData(donnee);
    
                        // Ajout du gestionnaire d'événements pour un seul élément sélectionné
                        cb.setOnAction(event -> {
                            handleSelectionUnique(cb);
                            donneeChoisies(); // Mettre à jour la liste des choix
                        });
    
                        choixTypeDonnees.getItems().add(cb);
                    }
                }
            }
    
            if (donneesSalles.isEmpty()) {
                System.out.println("Aucune donnée trouvée dans donneesSalles.");
            }
    
            // Vérification de l'ajout des éléments
            System.out.println("Éléments ajoutés au MenuButton :");
            for (MenuItem item : choixTypeDonnees.getItems()) {
                System.out.println("- " + item.getText());
            }
    
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors du chargement du fichier de configuration : " + e.getMessage());
        }
    }
    


    // Méthode pour récupérer les données choisies
    public void donneeChoisies() {
        ObservableList<MenuItem> obList = choixTypeDonnees.getItems();
        choices.clear(); // Réinitialiser la liste avant de récupérer la sélection
        boolean foundSelection = false;
    
        System.out.println("Éléments dans le menu déroulant :");
        for (MenuItem item : obList) {
            System.out.println("- " + item.getText());
            if (item instanceof CheckMenuItem) {
                CheckMenuItem checkMenuItem = (CheckMenuItem) item;
                System.out.println("  " + checkMenuItem.getText() + " sélectionné ? " + checkMenuItem.isSelected());
                if (checkMenuItem.isSelected()) {
                    choices.add(checkMenuItem.getText());
                    System.out.println("Donnée choisie mise à jour : " + checkMenuItem.getText());
                    foundSelection = true;
                    break;
                }
            }
        }
    
        if (!foundSelection) {
            System.out.println("Aucune donnée choisie dans le menu déroulant.");
        }
    }
    


    // Gérer le retour au menu précédent
    @FXML
    private void handleRetour() {
            application.loadMenuConfig();  // Retour à la page précédente
    }


    // Méthode pour vérifier si les TextFields contiennent des nombres valides
    private boolean areFieldsValid() {
        try {
            int minValue = Integer.parseInt(minValueField.getText());
            int maxValue = Integer.parseInt(maxValueField.getText());
            
            // Vérification que minValue <= maxValue
            if (minValue > maxValue) {
                System.out.println("Le seuil minimum ne peut pas être supérieur au seuil maximum.");
                return false;
            }
            return true;
        } catch (NumberFormatException e) {
            System.out.println("Erreur : Veuillez entrer des nombres valides.");
            return false;
        }
    }

    // Méthode pour mettre à jour les seuils dans le fichier config.ini
    private void updateConfigFile(int newMin, int newMax, String selectedType) {
        try {
            // Lire toutes les lignes du fichier
            List<String> lines = Files.readAllLines(Paths.get("Iot/config.ini"));
            List<String> updatedLines = new ArrayList<>();
            
            List<String> donneesSalles = new ArrayList<>(); // Liste pour stocker les types de données
            List<Integer> seuilMin = new ArrayList<>(); // Liste pour les seuils min
            List<Integer> seuilMax = new ArrayList<>(); // Liste pour les seuils max
            boolean dataSectionFound = false;
            boolean seuilSectionFound = false;
    
            // Extraire les données de la section [data] et [seuil] pour connaître leurs index
            for (String line : lines) {
                if (line.startsWith("donneesSalles")) {
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    donneesSalles = List.of(values.split(","))
                            .stream().map(v -> v.trim().replace("'", ""))
                            .collect(Collectors.toList());
                } else if (line.startsWith("seuil_min")) {
                    // Extraire les seuils min
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    seuilMin = List.of(values.split(","))
                            .stream().map(v -> Integer.parseInt(v.trim()))
                            .collect(Collectors.toList());
                } else if (line.startsWith("seuil_max")) {
                    // Extraire les seuils max
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    seuilMax = List.of(values.split(","))
                            .stream().map(v -> Integer.parseInt(v.trim()))
                            .collect(Collectors.toList());
                }
            }
    
            // Trouver l'index de la donnée sélectionnée
            int dataIndex = donneesSalles.indexOf(selectedType);
            
            if (dataIndex == -1) {
                System.out.println("Donnée non trouvée !");
                return;
            }
    
            // Modifier directement l'élément du tableau des seuils
            seuilMin.set(dataIndex, newMin);  // Mise à jour du seuil minimum pour l'index sélectionné
            seuilMax.set(dataIndex, newMax);  // Mise à jour du seuil maximum pour l'index sélectionné
    
            // Parcours du fichier de configuration pour reconstruire les lignes
            for (String line : lines) {
                if (line.startsWith("seuil_min")) {
                    // Mettre à jour seulement les seuils min modifiés
                    updatedLines.add("seuil_min=" + seuilMin.toString());
                } else if (line.startsWith("seuil_max")) {
                    // Mettre à jour seulement les seuils max modifiés
                    updatedLines.add("seuil_max=" + seuilMax.toString());
                } else {
                    updatedLines.add(line);  // Conserver les autres lignes inchangées
                }
            }
    
            // Réécrire uniquement les lignes modifiées
            Files.write(Paths.get("Iot/config.ini"), updatedLines);
            System.out.println("Fichier de configuration mis à jour avec succès.");
        } catch (IOException e) {
            e.printStackTrace();
            System.out.println("Erreur lors de la mise à jour du fichier de configuration.");
        }
    }
    
    
    
    






    // Gérer la validation des choix
    @FXML
    private void handleValider() {
        // Étape 1 : Vérifier que les valeurs des TextFields sont valides
        if (areFieldsValid()) {
            // Étape 2 : Récupérer les valeurs des TextFields
            int minValue = Integer.parseInt(minValueField.getText());
            int maxValue = Integer.parseInt(maxValueField.getText());

            // Étape 3 : Forcer la mise à jour des données choisies
            donneeChoisies();

            // Étape 4 : Vérifier si une donnée a été sélectionnée
            if (choices.isEmpty()) {
                System.out.println("Aucun type de donnée sélectionné !");
                return;
            }

            String selectedType = choices.get(0); // Obtenir le type sélectionné

            // Étape 5 : Mettre à jour les seuils dans l'énumération TypeDonnee
            TypeDonnee.setSeuilsByNom(selectedType, minValue, maxValue);
            System.out.println("Seuils mis à jour pour " + selectedType + ": " + minValue + " - " + maxValue);

            // Étape 6 : Mettre à jour le fichier de configuration
            updateConfigFile(minValue, maxValue, selectedType);

            // Étape 7 : Redémarrer le processus Python
            restartPythonScript();
        } else {
            System.out.println("Les champs sont invalides.");
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
}