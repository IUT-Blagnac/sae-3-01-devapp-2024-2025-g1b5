package sae;

import javafx.application.Application;
import javafx.stage.Stage;

public class App extends Application{

    @Override
    public void start(Stage primaryStage) throws Exception {
        System.out.println("La version de Java utilisée est :");
        System.out.println( System.getProperty("java.version") );
        System.out.println("Fin du programme");
    }
    public static void main(String[] args) {
       
        
    }

    
}
