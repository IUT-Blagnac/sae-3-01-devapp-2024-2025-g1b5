package sae.view;

import javafx.fxml.FXML;
import javafx.scene.control.ListView;
import javafx.scene.control.SelectionMode; // Correct import
import javafx.stage.Stage;
import sae.App;

import java.util.Arrays;
import java.util.List;

public class ParametrageSolar {

    private App application;
    private Stage fenetrePrincipale;

    @FXML
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
    }

    @FXML
    private void actionRetour() {
        application.loadMenu();
    }

    @FXML
    private void onSaveSelection() {
        // Récupérer les éléments sélectionnés
        List<String> selectedItems = listViewSolarData.getSelectionModel().getSelectedItems();
        System.out.println("Données sélectionnées : " + selectedItems);
        // Vous pouvez ici ajouter la logique pour utiliser ou sauvegarder la sélection
    }
}
