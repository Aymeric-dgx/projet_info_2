<?php

// Connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bourse";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Vérifie si une requête POST a été envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère l'ID du joueur depuis le formulaire
    $user_id = intval($_POST['player_id']); // ID envoyé via POST
} else {
    // Utilise l'ID de l'utilisateur connecté par défaut
    $user_id = $_SESSION['user_id'];
}

// Requête pour récupérer les actions de l'utilisateur
$stmt = $conn->query("SELECT nom, symbole, price, quantity FROM utilisateur u INNER JOIN global_wallet g ON u.id = g.id_user INNER JOIN action a ON g.id_action = a.id WHERE u.id = $user_id");
$actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>