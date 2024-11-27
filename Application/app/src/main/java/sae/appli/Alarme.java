package sae.appli;

public class Alarme {
    private String key;
    private double value;
    private String alarm_type;
    private String timestamp;

    public Alarme(String key, double value, String alarm_type, String timestamp) {
        this.key = key;
        this.value = value;
        this.alarm_type = alarm_type;
        this.timestamp = timestamp;
    }

    // Getters
    public String getKey() {
        return key;
    }

    public double getValue() {
        return value;
    }

    public String getAlarm_type() {
        return alarm_type;
    }

    public String getTimestamp() {
        return timestamp;
    }
}
