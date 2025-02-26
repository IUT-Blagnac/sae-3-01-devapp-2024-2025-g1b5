package sae.appli;

public enum TypeDonnee {
    temperature("temperature", 20, 30),
    humidity("humidity", 50, 70),
    co2("co2", 450, 1500),
    tvoc("tvoc", 200, 400),
    activity("activity", 100, 500),
    illumination("illumination", 1, 60),
    infrared("infrared", 1, 15),
    infrared_and_visible("infrared_and_visible", 1, 40),
    pressure("pressure", 980, 990);

    private final String nom;  // Nom de la donnée
    private int seuilMin;  // Seuil minimum (modifiable)
    private int seuilMax;  // Seuil maximum (modifiable)

    // Constructeur pour initialiser les données
    TypeDonnee(String nom, int seuilMin, int seuilMax) {
        this.nom = nom;
        this.seuilMin = seuilMin;
        this.seuilMax = seuilMax;
    }

    // Récupérer le nom de la donnée
    public String getNom() {
        return nom;
    }

    // Récupérer le seuil minimum
    public int getSeuilMin() {
        return seuilMin;
    }

    // Récupérer le seuil maximum
    public int getSeuilMax() {
        return seuilMax;
    }

    // Méthode pour récupérer les seuils sous forme d'un tableau
    public int[] getSeuils() {
        return new int[]{seuilMin, seuilMax};
    }

    // Méthode statique pour obtenir les seuils en fonction du nom de la donnée
    public static int[] getSeuilsByNom(String nom) {
        for (TypeDonnee donnee : TypeDonnee.values()) {
            if (donnee.getNom().equalsIgnoreCase(nom)) {
                return donnee.getSeuils();
            }
        }
        return null; // Retourne null si le nom n'est pas trouvé
    }

    // Méthode pour vérifier si un type donné est valide
    public static boolean containsType(String nom) {
        for (TypeDonnee donnee : TypeDonnee.values()) {
            if (donnee.getNom().equalsIgnoreCase(nom)) {
                return true;
            }
        }
        return false;
    }

    // Méthode pour modifier dynamiquement les seuils
    public static void setSeuilsByNom(String nom, int seuilMin, int seuilMax) {
        for (TypeDonnee donnee : TypeDonnee.values()) {
            if (donnee.getNom().equalsIgnoreCase(nom)) {
                donnee.seuilMin = seuilMin;
                donnee.seuilMax = seuilMax;
            }
        }
    }
}
