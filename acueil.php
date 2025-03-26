<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php
        session_start();

        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            // S'il n'est pas connecté, on le redirige vers la page de login
            header("Location: index.php");
            exit();
        }
        $servername = "localhost";
        $username = "root";
        $password = "";

        try {
            $bdd = new PDO("mysql:host=$servername;dbname=bourse;charset=utf8", $username, $password);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // ➤ Récupérer l'identifiant de l'utilisateur connecté
            $userId = $_SESSION['user_id'];
            $stmt = $bdd->prepare("SELECT identifiant FROM utilisateur WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $identifiant = $user ? htmlspecialchars($user['identifiant']) : 'Utilisateur';

            // On sélectionne toutes les actions
            $sql = "SELECT * FROM actions";
            $requete = $bdd->query($sql);
            $newsList = $requete->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "ERREUR : " . $e->getMessage();
            exit();
        }
    ?>
    <p style="text-align:center; font-weight: bold; font-size: 1.2em;">Connecté: <?php echo $identifiant; ?></p>
    <?php
         foreach($newsList as $action): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin: 10px;">
                <h2><?php echo htmlspecialchars($action['nom']); ?> (<?php echo htmlspecialchars($action['symbole']); ?>)</h2>
                <p><strong>Prix actuel :</strong> <?php echo number_format($action['prix_actuel'], 2); ?> €</p>
                <p><strong>ID de l'action :</strong> <?php echo $action['id']; ?></p>
            </div>
        <?php endforeach;
    ?>
</body>
</html>