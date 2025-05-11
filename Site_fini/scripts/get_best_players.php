<?php
// Afficher les meilleurs joueurs de la base de données (en fonction de leur valeur solde+actions)

// Informations de connexion + connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bourse";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql_request = 'SELECT u.id, pseudo, email, solde+COALESCE(SUM(price*quantity),0) AS total_value
                FROM utilisateur u
                LEFT JOIN global_wallet g ON u.id = g.id_user
                LEFT JOIN action a ON g.id_action = a.id
                GROUP BY u.id
                ORDER BY total_value DESC';


$result = $conn->query($sql_request);
$best_players = $result->fetchAll(PDO::FETCH_ASSOC);

// Affichage html

?>



<div class="whole_research_container">

<?php foreach($best_players as $best_player): ?>
    <div class="player_container">
        <div class="player_name_container">
            <h1> <?php echo $best_player['pseudo']; ?></h1>
            <h3> <?php echo $best_player['email']; ?></h3>
        </div>
        <div class="player_value_container">
            <h2> Valeur totale : <?php echo $best_player['total_value']; ?> €</h2>
            <div class="social_buttons_container">

                <form class="follow_user" method="POST">
                    <input type="hidden" name="player_id" value="<?php echo $best_player['id']; ?>">
                    <button type="submit" class="social_button">Suivre</button>
                </form>

                <form action="scripts/watch_user.php" method="POST">
                <input type="hidden" name="player_id" value="<?php echo $best_player['id']; ?>">
                    <input type="hidden" name="pseudo" value="<?php echo $best_player['pseudo']; ?>">
                    <input type="hidden" name="email" value="<?php echo $best_player['email']; ?>">
                    <button type="submit" class="social_button">Voir profil</button>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>