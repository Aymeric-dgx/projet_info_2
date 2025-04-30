<?php  

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
// récuperer la date actuel pour les historique
$stmt = $bdd->query("SELECT global_date FROM global_date LIMIT 1");
$resultat = $stmt->fetch(PDO::FETCH_ASSOC);
$date = $resultat['global_date'];


// Maj des actions 
$action = $bdd->query("SELECT * FROM action");
$resultat=$action->fetchAll(PDO::FETCH_ASSOC);
foreach($resultat as $action){
    $id_action=$action['id'];
    $price=$action['price'];
    $req = $bdd->query("INSERT INTO action_history(action_id,date,price_at_this_date) VALUES($id_action,'$date',$price)");
}

// Date 
$req = $bdd->query("UPDATE global_date SET global_date = DATE_ADD(global_date, INTERVAL 1 MONTH)");

// dividende
$req = $bdd->query("SELECT g.id_user,pourcentage,date_distribution,price,g.quantity,g.id_action FROM dividende d INNER JOIN action a on a.id_dividende=d.id INNER JOIN global_wallet g on g.id_action=a.id INNER JOIN utilisateur u on u.id=g.id_user;");
$result= $req->fetchAll(PDO::FETCH_ASSOC);

$date_actuel = date('n', strtotime($date)); 
foreach($result as $divi){
    $mois_distribution = date('n', strtotime($divi['date_distribution'])); 
    if($mois_distribution ==$date_actuel){
        $prix_dividende=(1+$divi['pourcentage']/100)*$divi['price'];
        $prix_dividende_joueur=$prix_dividende*$divi['quantity'];
        $user=$divi['id_user'];
        $action=$divi['id_action'];
        $req = $bdd->query("UPDATE utilisateur u SET solde = solde+$prix_dividende_joueur WHERE u.id=$user");

        //                            $req = $bdd->query("UPDATE action SET price = price-$prix_dividende WHERE id_action=$action");
    }
}



?>
