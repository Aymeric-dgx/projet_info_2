<?php 
// Ajouter un joueur à la liste de suivi de l'utilisateur connecté
session_start();
// Informations de connexion + connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bourse";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD']  === 'POST') {
    $follower_id = $_SESSION['user_id'];
    $followed_id = $_POST['player_id'];

    $sql_check = "SELECT COUNT(*) FROM follows WHERE id_follower = $follower_id AND id_followed = $followed_id ";
    $stmt_check = $conn->query($sql_check);
    $count = $stmt_check->fetchColumn();

    if($count == 0) {
        $sql_request = "INSERT INTO follows (id_follower, id_followed) VALUES ($follower_id, $followed_id) ";
        $stmt = $conn->query($sql_request);
        echo "c'est bon, le joueur a été ajouté à la liste de suivi !";
    } 
    else {
        echo "Vous suivez déja ce joueur !";
    }
}
?>