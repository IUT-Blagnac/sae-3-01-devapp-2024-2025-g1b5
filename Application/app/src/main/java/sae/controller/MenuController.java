package sae.controller;

import java.io.IOException;

import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.control.Button;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import sae.App;
import sae.view.MenuViewController;

public class MenuController {

    private BorderPane pane;
    private Stage stage;

   public static void loadSalles() {
        System.out.println("salut !");

        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(App.class.getResource("view/salles.fxml"));

            BorderPane vueListe = loader.load();

            //pane.setCenter(vueListe);
            

        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : salles.fxml");
            System.exit(1);
        }

   }
}
