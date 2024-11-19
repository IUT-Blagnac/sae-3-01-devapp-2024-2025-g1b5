package sae.view;


import javafx.fxml.FXML;
import javafx.scene.control.Button;
import sae.controller.MenuController;

public class MenuViewController  {
    
    @FXML
    private Button butSalles;
    @FXML
    private Button butSolar;
    @FXML
    private Button butAlarmes;
    @FXML
    private Button butTestCo;


    @FXML
    private void estPresse(){
        MenuController.loadSalles();
    }
    

   
}
