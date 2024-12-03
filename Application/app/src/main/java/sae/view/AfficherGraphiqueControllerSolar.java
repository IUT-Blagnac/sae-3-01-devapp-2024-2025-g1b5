package sae.view;

import java.io.File;
import java.io.FileReader;
import java.text.SimpleDateFormat;
import java.util.List;
import java.util.Date;

import org.json.simple.JSONObject;
import org.json.simple.parser.JSONParser;

import javafx.fxml.FXML;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Button;
import javafx.stage.Stage;
import sae.App;

public class AfficherGraphiqueControllerSolar {

    @SuppressWarnings("unused")
    private Stage fenetrePrincipale;

    private App application;

    @FXML
    private LineChart<Number, Number> lineChart;

    @FXML
    private Button butRetour;

    private JSONObject solarData; // Champ pour stocker les données JSON

    /**
     * Configure les données de la fenêtre principale et de l'application
     */
    public void setDatas(Stage fenetre, App app) {
        this.application = app;
        this.fenetrePrincipale = fenetre;
    }

    /**
     * Appelé lorsque le bouton "Retour" est cliqué
     */
    @FXML
    private void actionRetour() {
        Stage stage = (Stage) butRetour.getScene().getWindow();
        stage.close();
    }

    /**
     * Charger les données JSON et afficher le graphique
     */
    @FXML
    private void actionAfficherGraphique() {
        // Charger les données JSON
        chargerFichierSolar();

        if (solarData == null) {
            System.out.println("Aucune donnée chargée depuis le fichier JSON !");
            return;
        }

        // Afficher le graphique basé sur currentPower et lastUpdateTime
        afficherGraphique(solarData);
    }

    /**
     * Affiche le graphique en utilisant lastUpdateTime comme abscisse et currentPower comme ordonnée
     */
    private void afficherGraphique(JSONObject solarData) {
        // Effacer les données précédentes du graphique
        lineChart.getData().clear();

        JSONObject solar = (JSONObject) solarData.get("solar");
        if (solar == null) {
            System.out.println("Aucune donnée 'solar' trouvée dans le JSON.");
            return;
        }

        // Créer une série de données pour le graphique
        XYChart.Series<Number, Number> series = new XYChart.Series<>();
        series.setName("Current Power");

        // Définir le format de la date pour afficher correctement les dates sur l'axe X
        SimpleDateFormat dateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");

        // Itérer sur les clés de l'objet JSON
        int index = 1;  // Utiliser un index pour l'axe des X
        for (Object key : solar.keySet()) {
            JSONObject solarEntry = (JSONObject) solar.get(key);

            // Vérifier si l'attribut 'currentPower' existe et a une valeur
            if (solarEntry.containsKey("currentPower")) {
                JSONObject currentPower = (JSONObject) solarEntry.get("currentPower");
                if (currentPower.containsKey("power")) {
                    Double powerValue = (Double) currentPower.get("power");

                    // Vérifier si 'lastUpdateTime' existe et le convertir en long (timestamp)
                    if (solarEntry.containsKey("lastUpdateTime")) {
                        String lastUpdateTimeStr = (String) solarEntry.get("lastUpdateTime");

                        try {
                            // Convertir la chaîne de date en un objet Date et récupérer le timestamp
                            Date lastUpdateTime = dateFormat.parse(lastUpdateTimeStr);
                            long timestamp = lastUpdateTime.getTime();  // Obtenir le timestamp en millisecondes

                            // Ajouter un point à la série
                            series.getData().add(new XYChart.Data<>(timestamp, powerValue));
                        } catch (Exception e) {
                            System.out.println("Erreur lors de l'analyse de la date lastUpdateTime pour la clé " + key);
                        }
                    }
                }
            }
        }

        // Ajouter la série au graphique
        lineChart.getData().add(series);
    }

    /**
     * Charge le fichier JSON et stocke les données dans le champ solarData
     */
    public void chargerFichierSolar() {
        JSONParser parser = new JSONParser();

        try {
            // Définir le chemin du fichier solar.json à la racine du projet
            File file = new File("Iot/solar.json");

            if (!file.exists()) {
                System.out.println("Le fichier solar.json est introuvable à la racine du projet.");
                return;
            }

            // Lire et analyser le fichier JSON
            FileReader reader = new FileReader(file);
            JSONObject json = (JSONObject) parser.parse(reader);

            // Stocker les données dans le champ solarData
            this.solarData = json;

            System.out.println("Données extraites du fichier solar.json :");
            System.out.println(json.toJSONString());
            reader.close();

        } catch (Exception e) {
            System.out.println("Erreur lors du chargement du fichier solar.json : " + e.getMessage());
            e.printStackTrace();
        }
    }
}
