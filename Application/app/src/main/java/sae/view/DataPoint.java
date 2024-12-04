
package sae.view;


public class DataPoint {
    private long time; // Utiliser long pour stocker le temps (timestamp)
    private double power;

    // Constructeur
    public DataPoint(long time, double power) {
        this.time = time;
        this.power = power;
    }

    // Getters et Setters
    public long getTime() {
        return time;
    }

    public void setTime(long time) {
        this.time = time;
    }

    public double getPower() {
        return power;
    }

    public void setPower(double power) {
        this.power = power;
    }
}
