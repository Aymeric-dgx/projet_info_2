// C'est un truc de connexion et modification basique, mais adaptez le à votre/vos bases de données

<?php
// Informations de connexion + connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "tmp_bdd";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql_request = "INSERT INTO test(test) VALUES ('salut')";
$conn->exec($sql_request);
?>
