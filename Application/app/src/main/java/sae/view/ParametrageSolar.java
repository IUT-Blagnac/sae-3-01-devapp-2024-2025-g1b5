package sae.view;

import javafx.fxml.FXML;
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
import java.util.List;

public class ParametrageSolar {

    private Stage fenetrePrincipale;
    private App application;

    @FXML
    private Button butValider;
    @FXML
    private Button butRetour;
    @FXML
    private MenuButton choixTypeDonnees;

    // Liste qui contiendra les éléments sélectionnés
    private final ArrayList<String> selectedChoices = new ArrayList<>();
    private static final String CONFIG_FILE = "../Iot/config.ini";

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
        choixTypeDonnees.getItems().clear(); // Nettoyer le MenuButton avant d'ajouter les items
        selectedChoices.clear(); // Nettoyer la liste des choix sélectionnés
    
        try {
            // Lire le fichier de configuration
            List<String> lines = Files.readAllLines(Paths.get(CONFIG_FILE));
    
            // Chercher la ligne contenant les données à utiliser
            for (String line : lines) {
                if (line.startsWith("donneesSolar")) {
                    // Extraire les données entre crochets []
                    String values = line.substring(line.indexOf('[') + 1, line.indexOf(']'));
                    
                    // Créer une liste des éléments sélectionnés
                    List<String> selectedItems = List.of(values.split(","))
                            .stream()
                            .map(v -> v.trim().replace("'", "")) // Supprimer les guillemets et espaces
                            .toList();
    
                    System.out.println("Données extraites : " + selectedItems);
    
                    // Ajouter des CheckMenuItems pour chaque donnée présente dans le fichier config.ini
                    for (DonneeSolar donnee : DonneeSolar.values()) {
                        // Vérifier si cette donnée doit être affichée en fonction des éléments dans le config.ini
                        if (selectedItems.contains(donnee.name())) {
                            CheckMenuItem cb = new CheckMenuItem(donnee.name());
                            cb.setUserData(donnee);
    
                            // Cocher l'élément si il est dans la configuration
                            cb.setSelected(true);
                            selectedChoices.add(donnee.name()); // Ajouter la donnée sélectionnée dès le début
    
                            // Ajouter un listener pour gérer les sélections
                            cb.selectedProperty().addListener((observable, oldValue, newValue) -> {
                                if (newValue) {
                                    if (!selectedChoices.contains(donnee.name())) {
                                        selectedChoices.add(donnee.name());
                                        System.out.println("Ajouté : " + donnee.name());
                                    }
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
            }
        } catch (IOException e) {
            System.err.println("Erreur lors du chargement du fichier de configuration : " + e.getMessage());
        }
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
    

}
