<?php
// Script pour récupération de le solde de l'utilisateur

// Informations de connexion + connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bourse";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'ID du joueur depuis le formulaire
    $user_id = intval($_POST['player_id']); // ID envoyé via POST
} else {
    // Utilise l'ID de l'utilisateur connecté par défaut
    $user_id = $_SESSION['user_id'];
}

// Récupération du solde de l'utilisateur
$result = $conn->query("SELECT solde FROM utilisateur WHERE id = $user_id");
$solde = $result->fetch();

echo $solde['solde'];
?>