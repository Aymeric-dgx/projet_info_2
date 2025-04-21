<?php
// Script pour récupération de la valeur totale d'un utilisateur (solde + valeur des actions)

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

// Récupération de la valeur totale des actions de l'utilisateur
$result = $conn->query("SELECT SUM(average_price*quantity) AS total_value FROM global_wallet WHERE id_user = $user_id");
$value_action = $result->fetch();

if($value_action && $value_action['total_value'] != null) {
    $value_action = $value_action['total_value'];
} else {
    $value_action = 0;
}

// Récupération du solde de l'utilisateur
$result = $conn->query("SELECT solde FROM utilisateur WHERE id = $user_id");
$solde = $result->fetch();

//Valeur total
$total_value = $solde['solde'] + $value_action;
echo $total_value;

?>