package sae;

import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.layout.StackPane;
import javafx.scene.control.Button;
import javafx.stage.Stage;

public class App extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {
        // Création d'un bouton simple
        Button btn = new Button("Cliquez-moi");

        // Ajouter une action au bouton
        btn.setOnAction(event -> {
            System.out.println("Le bouton a été cliqué!");
        });

        // Créer le StackPane et ajouter le bouton
        StackPane root = new StackPane();
        root.getChildren().add(btn);

        // Créer une scène et la définir dans la fenêtre principale
        Scene scene = new Scene(root, 300, 250);
        primaryStage.setTitle("Mon Application JavaFX");
        primaryStage.setScene(scene);

        // Afficher la fenêtre
        primaryStage.show();
    }

    public static void main(String[] args) {
        // Appelle launch() qui déclenche l'initialisation JavaFX
        launch(args);
    }
}
