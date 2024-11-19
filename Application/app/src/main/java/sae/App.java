package sae;

import java.io.IOException;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Menu;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;

public class App extends Application{

    //@Override
    public void star1t(Stage primaryStage) throws Exception {
        System.out.println("La version de Java utilisée est :");
        System.out.println( System.getProperty("java.version") );
        System.out.println("Fin du programme");
    }
    public static void main(String[] args) {
       
        
    }

    private BorderPane rootPane;
    private Stage primaryStage;



    public void start(Stage primaryStage) {
        this.primaryStage = primaryStage;
        this.rootPane = new BorderPane();
        
        Scene scene = new Scene(rootPane);
        //scene.getStylesheets().add("css/style.css");
        primaryStage.setScene(scene);
        primaryStage.setTitle("Akari Alex");
        loadMenu();
        primaryStage.show();
        
       

    }

    public void loadMenu() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/Menu.fxml"));

            BorderPane vueListe = loader.load();

            Menu ctrl = loader.getController();

            //ctrl.setAkariApp(this);

            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : Menu.fxml");
            System.exit(1);
        }
    }

    
}
