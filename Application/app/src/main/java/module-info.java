module sae {
    requires javafx.controls;
    requires javafx.fxml;

    opens sae to javafx.fxml;
    exports sae;
}
