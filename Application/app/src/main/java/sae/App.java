package sae;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.appli.TypeDonnee;
import sae.view.AfficherDonneesController;
import sae.view.ConfigController;
import sae.view.MenuController;
import sae.view.ParametrageChoixSalles;
import sae.view.ParametrageSolar;
import sae.view.SolarConfigController;

import sae.appli.MqttAlarmListener;
import sae.view.AlarmPopUpController;


public class App extends Application {

    private BorderPane rootPane;
    private Stage stage;

    // Partager des données entre controllers
    private String numSalle;
    private ArrayList<String> donneesChoisies = new ArrayList<>();

    @Override
    public void start(Stage primaryStage) {
        this.stage = primaryStage;
        this.rootPane = new BorderPane();
        Scene scene = new Scene(rootPane);
        scene.getStylesheets().add(App.class.getResource("style.css").toExternalForm());
        stage.setScene(scene);
        loadMenu();
        primaryStage.setTitle("Menu");
        primaryStage.show();

        // Lancer l'écoute des alarmes MQTT
        startMqttListener();
    }

    // Méthode pour démarrer l'écouteur MQTT
    private void startMqttListener() {
        MqttAlarmListener mqttListener = new MqttAlarmListener();
        mqttListener.start();

        // Vérifier périodiquement si le fichier trigger.flag a été mis à jour
        new Thread(() -> {
            while (true) {
                checkForAlarms();
                try {
                    Thread.sleep(5000); // Vérifier toutes les 5 secondes
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
            }
        }).start();
    }

    // Méthode pour vérifier si une alarme a été déclenchée
    private void checkForAlarms() {
        AlarmPopUpController alarmPopUpController = new AlarmPopUpController();
        alarmPopUpController.showAlarmPopUp();
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

    public void loadDonnees() {
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

    public static void main2(String[] args) {
        Application.launch(args);
    }
}
