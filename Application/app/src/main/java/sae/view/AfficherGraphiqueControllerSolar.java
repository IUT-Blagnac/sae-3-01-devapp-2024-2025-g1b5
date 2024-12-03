package sae.view;

import javafx.application.Platform;
import javafx.scene.Scene;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.stage.Stage;
import javafx.util.StringConverter;
import org.json.simple.JSONObject;
import java.text.SimpleDateFormat;
import java.util.Date;

public class AfficherGraphiqueControllerSolar {

    private JSONObject solarData;

    // Constructeur qui prend un JSONObject contenant les données
    public AfficherGraphiqueControllerSolar(JSONObject solarData) {
        this.solarData = solarData;
    }

    // Méthode pour afficher le graphique dans une nouvelle fenêtre
    public void afficherGraphique() {
        // Exécuter la création du graphique sur le thread JavaFX
        Platform.runLater(() -> {
            Stage stage = new Stage();
            stage.setTitle("Graphique : Current Power vs Time");

            // Création des axes
            NumberAxis xAxis = new NumberAxis();
            xAxis.setLabel("Heure");

            // Limiter la plage de l'axe des X à 24 heures (24 * 60 * 60 * 1000 ms)
            xAxis.setLowerBound(0);  // Plage minimum en millisecondes (00:00:00)
            xAxis.setUpperBound(24 * 60 * 60 * 1000);  // Plage maximum en millisecondes (23:59:59)
            xAxis.setTickUnit(60 * 60 * 1000); // Intervalle de ticks : 1 heure (60 min * 60 s * 1000 ms)

            // Ajouter un StringConverter pour formater l'axe des X en heures et minutes
            xAxis.setTickLabelFormatter(new StringConverter<Number>() {
                private SimpleDateFormat sdf = new SimpleDateFormat("HH:mm");

                @Override
                public String toString(Number value) {
                    Date date = new Date(value.longValue());  // Convertir la valeur en Date
                    return sdf.format(date);  // Formater l'heure
                }

                @Override
                public Number fromString(String string) {
                    return null; // Cette méthode n'est pas nécessaire ici
                }
            });

            // Créer l'axe Y (Current Power)
            NumberAxis yAxis = new NumberAxis();
            yAxis.setLabel("Current Power");

            // Création du graphique
            LineChart<Number, Number> lineChart = new LineChart<>(xAxis, yAxis);
            lineChart.setTitle("Évolution de Current Power");

            // Ajouter des données au graphique
            XYChart.Series<Number, Number> series = new XYChart.Series<>();
            series.setName("Current Power");

            // Récupération des données à partir du JSON
            JSONObject solar = (JSONObject) solarData.get("solar");
            if (solar != null) {
                long firstTimestamp = -1;  // Utilisé pour calculer l'écart relatif
                for (Object key : solar.keySet()) {
                    JSONObject entry = (JSONObject) solar.get(key);
                    try {
                        // Extraire les données : lastUpdateTime et currentPower
                        String lastUpdateTimeStr = (String) entry.get("lastUpdateTime");
                        JSONObject currentPowerObj = (JSONObject) entry.get("currentPower");

                        if (lastUpdateTimeStr != null && currentPowerObj != null) {
                            // Convertir la date en timestamp (millisecondes)
                            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
                            Date date = sdf.parse(lastUpdateTimeStr);
                            long timestamp = date.getTime(); // timestamp en millisecondes

                            // Normalisation du timestamp par rapport à la première date
                            if (firstTimestamp == -1) {
                                firstTimestamp = timestamp;
                            }
                            long normalizedTimestamp = timestamp - firstTimestamp; // Conversion en nombre de millisecondes depuis le premier point

                            // Extraire la puissance actuelle
                            double currentPower = ((Number) currentPowerObj.get("power")).doubleValue();

                            // Ajouter les données au graphique
                            series.getData().add(new XYChart.Data<>(normalizedTimestamp, currentPower));
                        }
                    } catch (Exception e) {
                        System.out.println("Erreur lors du traitement des données pour l'entrée " + key + ": " + e.getMessage());
                    }
                }
            }

            // Ajouter la série de données au graphique
            lineChart.getData().add(series);

            // Créer une scène et afficher le graphique
            Scene scene = new Scene(lineChart, 800, 600);
            stage.setScene(scene);
            stage.show();
        });
    }
}
