<?php
// Afficher les joueurs dont le pseudo comment par la chaine de caractère rentré dans la barre de recherche

// Informations de connexion + connexion à la base de données
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bourse";

$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère la valeur du champ "search" envoyé par le formulaire
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';

    $sql = "SELECT u.id, pseudo, email, solde+COALESCE(SUM(price*quantity),0) AS total_value
            FROM utilisateur u
            LEFT JOIN global_wallet g ON u.id = g.id_user
            LEFT JOIN action a ON g.id_action = a.id
            WHERE pseudo LIKE :search
            GROUP BY u.id
            ORDER BY pseudo";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':search', $search . '%', PDO::PARAM_STR); // Ajoute le joker `%` après la chaîne
    $stmt->execute();
    $results_search = $stmt->fetchAll(PDO::FETCH_ASSOC);    
}
?>

<div class="whole_research_container">

<?php foreach($results_search as $best_player): ?>
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