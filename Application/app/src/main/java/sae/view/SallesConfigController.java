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

import java.util.ArrayList;

public class SallesConfigController {

    private static final String CONFIG_FILE = "Iot/config.ini";


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
            // Charger toutes les lignes du fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));

            // Créer la nouvelle ligne pour donneesSalles
            String newDonneesSallesLine = "donneesSalles=[" + donneesSalles.stream()
                    .map(attr -> "'" + attr + "'")
                    .collect(Collectors.joining(", ")) + "]";

            // Mettre à jour ou ajouter la ligne donneesSalles
            List<String> updatedLines = new ArrayList<>();
            boolean found = false;

            for (String line : lines) {
                if (line.startsWith("donneesSalles")) {
                    updatedLines.add(newDonneesSallesLine);
                    found = true;
                } else {
                    updatedLines.add(line);
                }
            }

            if (!found) {
                updatedLines.add(newDonneesSallesLine); // Ajouter si absent
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
}
