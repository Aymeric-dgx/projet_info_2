<?php
// Script pour récupération les follws de l'utilisateur connecté

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

$resultat = $conn->query("SELECT u.pseudo, u.email, u.solde 
                          FROM utilisateur u 
                          INNER JOIN follows f ON u.id = f.id_followed
                          WHERE f.id_follower = $user_id");
$follows = $resultat->fetchAll(PDO::FETCH_ASSOC);
?>