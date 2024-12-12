<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "Connect.inc.php";

if (isset($_SESSION['client_email']) || isset($_COOKIE['CidClient'])) {
    $email = isset($_SESSION['client_email']) ? $_SESSION['client_email'] : $_COOKIE['CidClient'];
    
    $query = $conn->prepare("SELECT role FROM Client WHERE email = :email");
    $query->bindParam(':email', $email);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!isset($result['role']) || $result['role'] === null) {
		echo "Vous devez disposer des droits d'administrateur pour accéder à cette page";
		header('Refresh: 5; URL=index.php');
        exit();
    }
} else {
	echo "Vous devez être connecté et disposer des droits d'administrateur pour accéder à cette page";
	header('Refresh: 5; URL=index.php');
    exit();
}

include "header.php";
?>