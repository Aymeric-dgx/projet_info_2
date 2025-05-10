<?php 
// Fichier php lancé toute les 2 minutes pour mettre à jour les données

session_start();  
$error_msg = "";  
$servername = "localhost";  
$username = "root";  
$password = "";  

try {  
    $bdd = new PDO("mysql:host=$servername;dbname=bourse", $username, $password);  
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
} catch (PDOException $e) {  
    echo "ERREUR : " . $e->getMessage();  
}  



// Récuperer la date actuel pour les historiques
$stmt = $bdd->query("SELECT global_date FROM global_date LIMIT 1");
$resultat = $stmt->fetch(PDO::FETCH_ASSOC);
$date = $resultat['global_date'];




// Maj de l'hsitorique des actions (prix + date)
$stmt = $bdd->query("SELECT id, price FROM action");
$resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($resultat as $row) {
    $id_action = $row['id'];
    $price = $row['price'];
    $bdd->query("INSERT INTO action_history (action_id, price_at_this_date, date) VALUES ($id_action, $price, '$date')");
}




// Maj de l'historique des soldes des joueurs (prix + date)
$stmt = $bdd->query("SELECT id, solde FROM utilisateur");
$resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($resultat as $row) {
    $id_user = $row['id'];
    $solde = $row['solde'];
    $bdd->query("INSERT INTO solde_history (id_user, solde_value, registement_date) VALUES ($id_user, $solde, '$date')");
}




// Maj de l'historique de la valeur total (solde + somme des actions) des joueurs (prix + date)
$sql = "SELECT u.id, solde+COALESCE(SUM(price*quantity),0) as total_value 
        FROM utilisateur u
        LEFT JOIN global_wallet gw ON u.id = gw.id_user
        LEFT JOIN action a ON gw.id_action = a.id
        GROUP BY u.id;";

$stmt = $bdd->query($sql);
$resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($resultat as $row) {
    $id_user = $row['id'];
    $total_value = $row['total_value'];
    $bdd->query("INSERT INTO total_value_history (id_user, total_value, registement_date) VALUES ($id_user, $total_value, '$date')");
}




// Maj du temps
$req = $bdd->query("UPDATE global_date SET global_date = DATE_ADD(global_date, INTERVAL 1 MONTH)");




// Maj du prix des actions
$stmt_all_actions = $bdd->query("SELECT * FROM action");
$all_actions = $stmt_all_actions->fetchAll(PDO::FETCH_ASSOC);

foreach($all_actions as $action) {
    $id_action = $action['id'];
    $previous_month_variation = $action['previous_month_variation'];
    $actual_price = $action['price'];

    do {
        $new_variation = $previous_month_variation + (mt_rand(-300, 300)/100)/100;
        $new_price = $actual_price * (1 + $new_variation);
    } while($new_price < 1 || $new_variation > 0.1 || $new_variation < -0.1);

    // Mise à jour du prix de l'action dans la base de données + enregistrement du new taux de variation
    $stmt_update = $bdd->query("UPDATE action SET price=$new_price WHERE id=$id_action");
    $stmt_update = $bdd->query("UPDATE action SET previous_month_variation=$new_variation WHERE id=$id_action");
}




// Versement des dividendes (si c'est le momenet d'en verser)
$req = $bdd->query("SELECT g.id_user,pourcentage,date_distribution,price,g.quantity,g.id_action FROM dividende d INNER JOIN action a on a.id_dividende=d.id INNER JOIN global_wallet g on g.id_action=a.id INNER JOIN utilisateur u on u.id=g.id_user;");
$result= $req->fetchAll(PDO::FETCH_ASSOC);

$date_actuel = date('n', strtotime($date)); 
foreach($result as $divi){
    $mois_distribution = date('n', strtotime($divi['date_distribution'])); 
    if($mois_distribution ==$date_actuel){
        $prix_dividende = ($divi['pourcentage']/100) * $divi['price'];
        $prix_dividende_joueur = $prix_dividende * $divi['quantity'];
        $use = $divi['id_user'];
        $action = $divi['id_action'];
        $req = $bdd->query("UPDATE utilisateur u SET solde = solde+$prix_dividende_joueur WHERE u.id=$user");
    }
}


?>
