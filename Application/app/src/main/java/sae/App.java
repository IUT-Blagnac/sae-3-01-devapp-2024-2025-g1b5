package sae;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Parent;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.appli.TypeDonnee;
import sae.view.AfficherDonneesControllerSolar;
import sae.view.AfficherDonneesController;
import sae.view.ConfigController;
import sae.view.MenuController;
import sae.view.ParametrageChoixSalles;
import sae.view.ParametrageSolar;
import sae.view.SolarConfigController;

public class App extends Application{

    private BorderPane rootPane;
    private Stage stage;
    



    //partager des données entre controllers
    private String numSalle;
    ArrayList<String> donneesChoisies = new ArrayList<>();


    @Override
    public void start(Stage primaryStage)  {
        this.stage = primaryStage;
        this.rootPane = new BorderPane();
        Scene scene = new Scene(rootPane);
        scene.getStylesheets().add(App.class.getResource("style.css").toExternalForm());
        stage.setScene(scene);
        loadMenu();
        primaryStage.setTitle("Menu");
        primaryStage.show();

    }

    public void loadMenu() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/menu.fxml"));

            BorderPane vueListe = loader.load();

            MenuController menu = loader.getController();
            menu.setDatas(stage, this);
            
            this.rootPane.setCenter(vueListe);
            

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : menu.fxml");
            System.exit(1);
        }
    }

    

    public void loadParametrageSalles() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/salles.fxml"));
            BorderPane vueListe = loader.load();

            TypeDonnee[] donnees = TypeDonnee.values();
            // Convertir en listes
            List<TypeDonnee> listTypeDonnee = Arrays.asList(donnees);

            ParametrageChoixSalles choixSalles = loader.getController();
            choixSalles.setDatas(stage, this);

            this.numSalle = choixSalles.getSalle();
            this.donneesChoisies = choixSalles.getTabDonnee();
            
            choixSalles.loadMenuDeroulantDonnees(listTypeDonnee);
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : salles.fxml");
            System.exit(1);
        }
    }

    public void loadParametrageSolar() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/solar.fxml"));
            BorderPane vueListe = loader.load();

            ParametrageSolar choixSolar = loader.getController();
            choixSolar.setDatas(stage, this);
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : solar.fxml");
            System.exit(1);
        }
    }

    public void loadSolarConfig() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/solarConfig.fxml"));
            BorderPane vueListe = loader.load();

            SolarConfigController configSolar = loader.getController();
            configSolar.setDatas(stage, this);
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : solarConfig.fxml");
            System.exit(1);
        }
    }


    public void loadMenuConfig() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/menuConfig.fxml"));
            BorderPane vueListe = loader.load();

            ConfigController config = loader.getController();
            config.setDatas(stage, this);
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : menuConfig.fxml");
            System.exit(1);
        }
    }

    public void loadDonnees(){
      try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/donnee.fxml"));

            BorderPane vueListe = loader.load();

            AfficherDonneesController affichage = loader.getController();
            
            affichage.setDatas(stage, this);
            affichage.setSalle(this.numSalle);
            affichage.setTab(donneesChoisies);
            
            affichage.afficherDonnees();
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : donnee.fxml");
            System.exit(1);
        }
    }

    public void loadDonneesSelectionnees(List<String> selectedChoices) {
        try {
            // Charger la vue pour afficher les données
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/sae/view/donneeSolar.fxml"));
            Parent root = loader.load();
            
            // Obtenez le contrôleur de la nouvelle page
            AfficherDonneesControllerSolar controller = loader.getController();
            
            // Charger les données du fichier JSON et les filtrer en fonction des choix sélectionnés
            controller.setTab(selectedChoices);  // Passer les choix sélectionnés à la page suivante
            
            // Afficher la scène avec les données
            Stage stage = new Stage();
            stage.setScene(new Scene(root));
            stage.show();

        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    

    

    public static void main2(String[] args) {
        Application.launch(args);   
    }

}
