<?php
require_once 'vendor/autoload.php';
include 'Connect.inc.php';

$faker = Faker\Factory::create('fr_FR');
$tabProduit=[];
$tabCategorie=[];
$tabClient=[];

for($i=0;$i<10;$i++){
    $tabClient = [
        'idClient' => $faker->randomDigitNotNull,
        'mailClient' => $faker->email,
        'nomClient' => $faker->name,
        'prenomClient' => $faker->name,
        'telClient' => $faker->phoneNumber,
        'mdpClient' => $faker->password,
        'genre' => $faker->randomElement($array = array ('H','F')),
        'dateNaissance' => $faker->date($format = 'Y-m-d', $max = 'now'),
        'role'=> 'client',
        'idAdresse' => $faker->randomDigitNotNull
    ];
}
var_dump($tabClient);


 

$client = [];



