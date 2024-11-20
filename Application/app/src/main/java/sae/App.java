package sae;

import java.io.IOException;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;

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
            
            this.rootPane.setCenter(vueListe);

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : menu.fxml");
            System.exit(1);
        }
    }

    public static void main2(String[] args) {
       Application.launch(args);   
    }

    
}
