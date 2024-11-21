package sae;

import java.io.IOException;
import java.util.Arrays;
import java.util.List;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.appli.TypeDonnee;
import sae.view.MenuController;
import sae.view.ParametrageChoixSalles;

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

    public static void main2(String[] args) {
       Application.launch(args);   
    }

    public void loadParametrageSalles() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/salles.fxml"));
            BorderPane vueListe = loader.load();

            TypeDonnee[] donnees = TypeDonnee.values();
            // Convertir en liste
            List<TypeDonnee> listTypeDonnee = Arrays.asList(donnees);

            ParametrageChoixSalles choixSalles = loader.getController();
            choixSalles.setDatas(stage, this);
            
            choixSalles.loadMenuDeroulant(listTypeDonnee);
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : salles.fxml");
            System.exit(1);
        }
    }

    
}
