<?php
  try{
    $user = 'R2024MYSAE3010';
    $pass = 'q7N5pgzSW398gK';
    $conn = new PDO('mysql:host=localhost:3306;dbname=R2024MYSAE3010;charset=UTF8'  
            ,$user, $pass, array(PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION));
  }
  catch (PDOException $e){
    echo "Erreur: ".$e->getMessage()."<br>";
    die() ;
  }
?>  