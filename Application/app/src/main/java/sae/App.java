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
import sae.view.AppState;
import sae.view.AfficherDonneesController;
import sae.view.ConfigController;
import sae.view.MenuController;
import sae.view.ParametrageChoixSalles;
import sae.view.ParametrageSolar;
import sae.view.SallesConfigController;
import sae.view.SolarConfigController;

public class App extends Application{

    private BorderPane rootPane;
    private Stage stage;
    private Process pythonProcess;



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
        startPythonScript();

    }

    private void startPythonScript() {
        Thread pythonThread = new Thread(() -> {
            try {
                // Lancer le processus Python
                pythonProcess = new ProcessBuilder("python", "Iot/main2.py").start();
                long pid = pythonProcess.pid();

                // Sauvegarder le PID dans AppState
                AppState.setPythonPID(pid);
                System.out.println("Processus Python démarré avec PID : " + pid);
            } catch (IOException e) {
                e.printStackTrace();
                System.out.println("Erreur lors du lancement du script Python.");
            }
        });

        pythonThread.setDaemon(true); // S'assurer que le thread se termine avec l'application
        pythonThread.start();
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
    public void loadSallesConfig() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/sallesConfig.fxml"));
            BorderPane vueListe = loader.load();

            SallesConfigController configSalles = loader.getController();
            configSalles.setDatas(stage, this);
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : sallesConfig.fxml");
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
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/sae/view/donneeSolar.fxml"));
            Parent root = loader.load();
    
            // Récupérer le contrôleur d'affichage
            AfficherDonneesControllerSolar controller = loader.getController();
            controller.setDatas(stage, this);
            controller.setTab(selectedChoices); // Transmettre les choix sélectionnés
    
            Stage stage = new Stage();
            stage.setScene(new Scene(root));
            stage.show();
    
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    
        @Override
    public void stop() {
        System.out.println("Fermeture de l'application...");
        stopPythonProcess();
    }

    // Méthode pour arrêter le processus Python
    private void stopPythonProcess() {
        long pid = AppState.getPythonPID();
        if (pid > 0) {
            try {
                // Détection du système d'exploitation
                String os = System.getProperty("os.name").toLowerCase();

                Process process;
                if (os.contains("win")) {
                    // Commande Windows : arrêter le processus avec "taskkill"
                    process = new ProcessBuilder("cmd", "/c", "taskkill /PID " + pid + " /F").start();
                } else if (os.contains("nix") || os.contains("nux") || os.contains("mac")) {
                    // Commande Linux/Mac : arrêter le processus avec "kill -9"
                    process = new ProcessBuilder("kill", "-9", String.valueOf(pid)).start();
                } else {
                    System.out.println("Système d'exploitation non pris en charge pour arrêter le processus Python.");
                    return;
                }

                int exitCode = process.waitFor();
                if (exitCode == 0) {
                    System.out.println("Processus Python avec PID " + pid + " arrêté avec succès.");
                } else {
                    System.out.println("Échec de l'arrêt du processus Python avec PID " + pid);
                }

                // Réinitialiser le PID après l'arrêt
                AppState.setPythonPID(-1);

            } catch (IOException | InterruptedException e) {
                e.printStackTrace();
                System.out.println("Erreur lors de l'arrêt du processus Python.");
            }
        } else {
            System.out.println("Aucun processus Python actif à arrêter.");
        }
    }


    public static void main2(String[] args) {
        Application.launch(args);   
    }

}
