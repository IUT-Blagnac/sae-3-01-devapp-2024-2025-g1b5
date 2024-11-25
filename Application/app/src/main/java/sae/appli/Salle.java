package sae.appli;

import java.sql.Array;
import java.util.ArrayList;

public class Salle {
    private String nom;
    private ArrayList<TypeDonnee> donnees;

    public Salle(String nom){
        this.nom = nom;
        this.donnees = new ArrayList<TypeDonnee>();
    }
    public String getNom(){
        return this.nom;
    }
    public void ajouterDonnee(TypeDonnee donnee){
        this.donnees.add(donnee);
    }
    public void retirerDonnee(TypeDonnee donnee){
        this.donnees.remove(donnee);
    }
    public ArrayList<TypeDonnee> getDonnees(){
        return this.donnees;
    }
    }