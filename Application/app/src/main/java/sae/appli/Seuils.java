package sae.appli;

import java.util.List;

public class Seuils {
    private String nom;
    private int seuilMin;
    private int seuilMax;

    // Liste statique des seuils
    private static final List<Seuils> seuilsList = List.of(
        new Seuils("Température", 15, 30),
        new Seuils("Humidité", 40, 60)
        // Ajouter d'autres seuils ici
    );

    // Constructeur
    public Seuils(String nom, int seuilMin, int seuilMax) {
        this.nom = nom;
        this.seuilMin = seuilMin;
        this.seuilMax = seuilMax;
    }

    // Méthode publique pour rechercher un seuil par nom
    public static Seuils findSeuilByNom(List<Seuils> seuilsList, String nom) {
        for (Seuils seuil : seuilsList) {
            if (seuil.getNom().equalsIgnoreCase(nom)) {
                return seuil;  // Retourne l'objet Seuils correspondant
            }
        }
        return null;  // Si aucun seuil n'a été trouvé
    }

    // Getters et Setters
    public String getNom() {
        return nom;
    }

    public void setNom(String nom) {
        this.nom = nom;
    }

    public int getSeuilMin() {
        return seuilMin;
    }

    public void setSeuilMin(int seuilMin) {
        this.seuilMin = seuilMin;
    }

    public int getSeuilMax() {
        return seuilMax;
    }

    public void setSeuilMax(int seuilMax) {
        this.seuilMax = seuilMax;
    }

    // Méthode pour obtenir tous les seuils
    public static List<Seuils> getAllSeuils() {
        return seuilsList;
    }

    @Override
    public String toString() {
        return nom + " (Min: " + seuilMin + ", Max: " + seuilMax + ")";
    }
}
