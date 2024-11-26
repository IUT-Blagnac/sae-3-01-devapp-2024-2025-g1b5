package sae;

import java.io.IOException;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.view.MenuController;
import sae.view.ParametrageChoixSalles;
import sae.view.ParametrageSolar;

public class App extends Application{

    private BorderPane rootPane;
    private Stage stage;

    @Override
    public void start(Stage primaryStage)  {

        this.stage = primaryStage;
        this.rootPane = new BorderPane();

        Scene scene = new Scene(rootPane);
        stage.setScene(scene);
        
        loadMenu();

        primaryStage.setTitle("Menu");
        primaryStage.show();

        // Démarrer le script Python main2.py
        startPythonScript();
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

            ParametrageChoixSalles choixSalles = loader.getController();
            choixSalles.setDatas(stage, this);
            
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

    // Fonction pour démarrer le script Python sans bloquer l'interface
    private void startPythonScript() {
        Thread pythonThread = new Thread(() -> {
            try {
                // Création d'un processus pour exécuter le script Python
                ProcessBuilder processBuilder = new ProcessBuilder("python", "Iot/main2.py");
                processBuilder.inheritIO();  // Permet d'afficher les sorties du script Python dans la console Java
                Process process = processBuilder.start(); // Démarrer le processus
                process.waitFor();  // Attendre que le processus Python se termine
            } catch (IOException | InterruptedException e) {
                e.printStackTrace();
                System.out.println("Erreur lors du démarrage du script Python.");
            }
        });

        pythonThread.setDaemon(true); // Permet d'arrêter ce thread quand l'application se ferme
        pythonThread.start(); // Démarre le thread
    }

    public static void main2(String[] args) {
        Application.launch(args);   
    }

    
}
