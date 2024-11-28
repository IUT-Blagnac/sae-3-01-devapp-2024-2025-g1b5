package sae.appli;
import com.fasterxml.jackson.annotation.JsonProperty;
import java.util.Map;

public class Alarme {
    private Map<String, Map<String, AlarmDetails>> alarmes;

    public Map<String, Map<String, AlarmDetails>> getAlarmes() {
        return alarmes;
    }

    public void setAlarmes(Map<String, Map<String, AlarmDetails>> alarmes) {
        this.alarmes = alarmes;
    }

    public static class AlarmDetails {
        private String key;
        private double value;
        private String alarm_type;
        private String timestamp;

        // Constructeurs, getters et setters
        public AlarmDetails() { }

        public AlarmDetails(String key, double value, String alarm_type, String timestamp) {
            this.key = key;
            this.value = value;
            this.alarm_type = alarm_type;
            this.timestamp = timestamp;
        }

        public String getKey() {
            return key;
        }

        public void setKey(String key) {
            this.key = key;
        }

        public double getValue() {
            return value;
        }

        public void setValue(double value) {
            this.value = value;
        }

        public String getAlarm_type() {
            return alarm_type;
        }

        public void setAlarm_type(String alarm_type) {
            this.alarm_type = alarm_type;
        }

        public String getTimestamp() {
            return timestamp;
        }

        public void setTimestamp(String timestamp) {
            this.timestamp = timestamp;
        }
    }
}
