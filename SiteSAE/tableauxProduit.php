<?php
require_once __DIR__ . '/../vendor/autoload.php';
include 'Connect.inc.php';

$faker = Faker\Factory::create('fr_FR');
$tabProduit=[
    'Jeux de société' => ['Monopoly', 'Uno', 'Cluedo' , 'Jungle Speed' ,'SkyJo'],
    'Jeux de rôle' => ['Donjons et Dragons', 'Pathfinder', 'Star Wars' ],
    'Jeux de logique' => ['Rush Hour', 'Mastermind', 'Sudoku' ],
    'Jeux de mémoire' => ['Memory', 'Dobble', 'Time’s Up' ],
    'Kits de robotique' => ['Lego Mindstorms', 'Makeblock', 'Thymio' ],
    'Kits de chimie' => ['Clementoni', 'Buki', 'Educa' ],
    'Marvel' => ['Spiderman', 'IronMan', 'Captain America' ],
    'DC Comics' => ['Batman', 'SuperMan ','WonderWoman' ],
    'Power Rangers' => ['Mighty Morphin', 'Zeo', 'Turbo' ],
    'Animaux' => ['peppa pig', 'paw patrol', 'Les Animaux de la Ferme ' ],
    'Jeux de jardin' => ['Toboggan', 'Balançoire', 'Bac à sable' ],
    'Vélos et trottinettes' => ['Vélo', 'Trottinette', 'Draisienne' ,'skateboard'],
    'Instruments' => ['Guitare', 'Piano', 'Batterie' ],
    'Kits de chant' => ['Micro', 'Karaoké', 'Enceinte' ,'Casque'],
    'Arts plastiques' => ['Peinture', 'Pâte à modeler', 'Crayons de couleur' ],
    'DIY' => ['Perles', 'Origami', 'Couture' ],
    'Tablettes pour enfants' => ['Vtech', 'Leapfrog', 'Kurio' ],
    'Appareils photo et caméras' => ['Vtech', 'Fisher Price', 'Nikon' ],
    'Jouets d’éveil' => ['Sophie la Girafe', 'Tapis d’éveil', 'Mobile' ],
    'Jeux pour bébé' => ['Cubes', 'Puzzles', 'Livres' ],
    'Ballons' => ['Foot', 'Basket', 'Rugby' ],
    'Raquettes' => ['Tennis', 'Badminton', 'Ping Pong' ],


];
$tabCategorie = [
    'Familial' => [
        'Jeux de société' ,
        'Jeux de rôle' 
    ],
    'Éducatif' => [
        'Jeux de logique' ,
        'Jeux de mémoire' 
    ],
    'Scientifique' => [
        'Kits de robotique' ,
        'Kits de chimie' 
    ],
    'Figurine' => [
        'Super-héros' => ['Marvel', 'DC Comics', 'Power Rangers'],
        'Animaux' ,
    ],
    'Exterieur' => [
        'Jeux de jardin' ,
        'Vélos et trottinettes' ,
    ],
    'Musical' => [
        'Instruments' ,
        'Kits de chant' ,
    ],
    'Créatif' => [
        'Arts plastiques' ,
        'DIY' ,
    ],
    'Technologique' => [
        'Tablettes pour enfants' ,
        'Appareils photo et caméras' ,
    ],
    'Bébé' => [
        'Jouets d’éveil' ,
        'Jeux pour bébé' ,
    ],
    'Sport' => [
        'Ballons'  ,
        'Raquettes' ,
    ],
];



$tabClient=[];
$tabAdresse=[];



$numbers = range(1, 50);
shuffle($numbers);
for($i=0;$i<10;$i++){
    $Client = [
        'idClient' => $faker->randomDigitNotNull,
        'mailClient' => $faker->email,
        'nomClient' => $faker->firstName,
        'prenomClient' => $faker->lastName,
        'telClient' => $faker->phoneNumber,
        'mdpClient' => $faker->password,
        'genre' => $faker->randomElement($array = array ('H','F')),
        'dateNaissance' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'idAdresse' => $numbers[$i]
    ];
    $tabClient[] = $Client;
}
/*
foreach($tabClient as $client){
$insert = 'INSERT INTO client (idClient, mailClient, nomClient, prenomClient, telClient, mdpClient, genre, dateNaissance, idAdresse) VALUES ('.$client['idClient'].','.$client['mailClient'].','.$client['nomClient'].','.$client['prenomClient'].','.$client['telClient'].','.$client['mdpClient'].','.$client['genre'].','.$client['dateNaissance'].','.$client['idAdresse'].')';
print_r($insert);
    echo '<br>';
    echo '<br>';

}

for($i=0;$i<10;$i++){
    $Adresse = [
        'idAdresse' => $numbers[$i],
        'rue' => $faker->streetName,
        'ville' => $faker->city,
        'codePostal' => $faker->postcode,
        'pays' => 'France'
    ];
    $tabAdresse[] = $Adresse;
}

foreach($tabAdresse as $adresse){
    $insert = 'INSERT INTO adresse (idAdresse, rue, ville, codePostal, pays) VALUES ('.$adresse['idAdresse'].','.$adresse['rue'].','.$adresse['ville'].','.$adresse['codePostal'].','.$adresse['pays'].')';
}
*/
//Insertion des catégories
//tout en minuscule
function getInitials($phrase) {
    $mots = explode(' ', $phrase); // Séparer les mots par espace
    $initiales = '';

    foreach ($mots as $mot) {
        $initiales .= mb_substr(mb_strtolower($mot, 'UTF-8'), 0, 1, 'UTF-8'); // Récupérer la première lettre de chaque mot
    }

    return $initiales;
}


foreach($tabCategorie as $categorie => $sousCategorie){
    $valCategorie = mb_substr(mb_strtolower($categorie, 'UTF-8'),0,3, 'UTF-8');
    echo '<br>';
    echo '<br>';

    $insert = "INSERT INTO categorie (nomCategorie,valCategorie) VALUES ('$categorie','$valCategorie')";
    echo '<br>';
    echo '<br>';

    foreach($sousCategorie as $sousCat => $sousCat2){
        if(is_array($sousCat2)){
            foreach($sousCat2 as $sousCat3){
                $valSousCategorie = mb_substr(mb_strtolower($sousCat3, 'UTF-8'),0,3, 'UTF-8');
                $insert = "INSERT INTO categorie (nomCategorie,valCategorie) VALUES ('$sousCat3','$valSousCategorie');";
                
                print_r($insert);

                echo '<br><br>';

            }
        }else{
        
                $valSousCategorie = getInitials($sousCat2);
             // Récupérer les initiales enlever espace
                $valSousCategorie = str_replace(' ', '', $valSousCategorie);
                if (strlen($valSousCategorie) < 3) {
                    $valSousCategorie = mb_substr($sousCat2, 0, 3); 
                }
                echo '<br><br>';

                $insert = "INSERT INTO categorie (nomCategorie, valCategorie) VALUES ('$sousCat2', '$valSousCategorie');";
                echo $insert;
        }
    }
   
    
}






 




