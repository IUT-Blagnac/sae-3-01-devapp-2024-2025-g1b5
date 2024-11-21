package sae.appli;

import java.util.List;

public class Donnees {

    private List<TypeDonnee> listDonnees ;

    public int getNbDonnees(){
        return listDonnees.size();
    }

    public void ajouterDonnee(TypeDonnee nouvelleDonnee){
        listDonnees.add(nouvelleDonnee);
    }

    public void retirerDonnee(TypeDonnee ancienneDonnee){
        listDonnees.remove(ancienneDonnee);
    }

}
