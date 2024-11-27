package sae.view;

import javafx.fxml.FXML;
<<<<<<< Updated upstream
import javafx.scene.control.ListView;
import javafx.scene.control.SelectionMode; // Correct import
import javafx.stage.Stage;
import sae.App;

import java.util.Arrays;
=======
import javafx.scene.control.Button;
import javafx.scene.control.CheckMenuItem;
import javafx.scene.control.MenuButton;
import javafx.stage.Stage;
import sae.App;
import sae.appli.DonneeSolar;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.ArrayList;
>>>>>>> Stashed changes
import java.util.List;

public class ParametrageSolar {

<<<<<<< Updated upstream
=======
    private Stage fenetrePrincipale;
>>>>>>> Stashed changes
    private App application;
    private Stage fenetrePrincipale;

    @FXML
<<<<<<< Updated upstream
    private ListView<String> listViewSolarData;

    // Liste des données de solar à afficher dans la ListView
    private List<String> donneesSolar = Arrays.asList(
        "currentPower", "lastDayData", "lastMonthData", "lastYearData", 
        "lifeTimeData", "lastUpdateTime"
    );

    public void setDatas(Stage fenetrePrincipale, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetrePrincipale;
        
        // Initialisation de la ListView avec les données
        listViewSolarData.getItems().addAll(donneesSolar);
        
        // Activation de la sélection multiple avec SelectionMode
        listViewSolarData.getSelectionModel().setSelectionMode(SelectionMode.MULTIPLE);
=======
    private Button butValider;
    @FXML
    private Button butRetour;
    @FXML
    private MenuButton choixTypeDonnees;

    // Liste qui contiendra les éléments sélectionnés
    private final ArrayList<String> selectedChoices = new ArrayList<>();
    private static final String CONFIG_FILE = "Iot/config.ini";

    /**
     * Initialise les données du contrôleur.
     *
     * @param fenetre La fenêtre principale.
     * @param app     L'application principale.
     */
    public void setDatas(Stage fenetre, App app) {
        this.fenetrePrincipale = fenetre;
        this.application = app;
        loadMenuDeroulantDonnees();
    }

    /**
     * Charge les options du menu déroulant depuis le fichier de configuration.
     */
    public void loadMenuDeroulantDonnees() {
        choixTypeDonnees.getItems().clear(); // Nettoie le MenuButton avant d'ajouter les items

        try {
            // Lire le fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
            for (String line : lines) {
                if (line.startsWith("donneesSolar")) {
                    // Extraire les données entre crochets []
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    List<String> selectedItems = List.of(values.split(","))
                            .stream()
                            .map(v -> v.trim().replace("'", "")) // Supprime les guillemets
                            .toList();

                    System.out.println("Données extraites : " + selectedItems);

                    // Ajouter des CheckMenuItems pour chaque donnée
                    for (DonneeSolar donnee : DonneeSolar.values()) {
                        CheckMenuItem cb = new CheckMenuItem(donnee.name());
                        cb.setUserData(donnee);

                        // Si la donnée est dans le fichier config, on la coche par défaut
                        if (selectedItems.contains(donnee.name())) {
                            cb.setSelected(true);
                            selectedChoices.add(donnee.name()); // Ajouter la donnée sélectionnée dès le début
                        }

                        // Ajouter un listener pour gérer les sélections
                        cb.selectedProperty().addListener((observable, oldValue, newValue) -> {
                            if (newValue) {
                                selectedChoices.add(donnee.name());
                                System.out.println("Ajouté : " + donnee.name());
                            } else {
                                selectedChoices.remove(donnee.name());
                                System.out.println("Retiré : " + donnee.name());
                            }
                        });

                        // Ajouter l'élément au MenuButton
                        choixTypeDonnees.getItems().add(cb);
                    }
                }
            }
        } catch (IOException e) {
            System.err.println("Erreur lors du chargement du fichier de configuration : " + e.getMessage());
        }
>>>>>>> Stashed changes
    }

    /**
     * Retourne les choix sélectionnés.
     *
     * @return Une liste des choix sélectionnés.
     */
    public List<String> getTabDonnee() {
        return selectedChoices;
    }

    /**
     * Action liée au bouton Retour.
     */
    @FXML
    private void actionRetour() {
        application.loadMenu();
    }

<<<<<<< Updated upstream
    @FXML
    private void onSaveSelection() {
        // Récupérer les éléments sélectionnés
        List<String> selectedItems = listViewSolarData.getSelectionModel().getSelectedItems();
        System.out.println("Données sélectionnées : " + selectedItems);
        // Vous pouvez ici ajouter la logique pour utiliser ou sauvegarder la sélection
    }
=======
    /**
     * Action liée au bouton Valider.
     */
    @FXML
    private void actionValider() {
        System.out.println("Liste des données sélectionnées : " + selectedChoices);
        
        if (!selectedChoices.isEmpty()) {
            System.out.println("Données sélectionnées : " + selectedChoices);
    
            // Appeler la méthode pour charger et afficher les données
            application.loadDonneesSelectionnees(selectedChoices);  // Passe les données sélectionnées
        } else {
            System.out.println("Veuillez sélectionner des données !");
        }
    }
    

>>>>>>> Stashed changes
}
