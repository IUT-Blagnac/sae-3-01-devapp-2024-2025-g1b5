package sae.appli;

public class Alarme {
    private String key;
    private double value;
    private String alarmType;
    private String timestamp;

    // Constructeur
    public Alarme(String key, double value, String alarmType, String timestamp) {
        this.key = key;
        this.value = value;
        this.alarmType = alarmType;
        this.timestamp = timestamp;
    }

    // Getters et setters
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

    public String getAlarmType() {
        return alarmType;
    }

    public void setAlarmType(String alarmType) {
        this.alarmType = alarmType;
    }

    public String getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(String timestamp) {
        this.timestamp = timestamp;
    }

    @Override
    public String toString() {
        return "Alarme{" +
                "key='" + key + '\'' +
                ", value=" + value +
                ", alarmType='" + alarmType + '\'' +
                ", timestamp='" + timestamp + '\'' +
                '}';
    }
}
