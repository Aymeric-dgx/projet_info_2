<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css">
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
            $stmt = $bdd->prepare("SELECT pseudo FROM utilisateur WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $pseudo = $user ? htmlspecialchars($user['pseudo']) : 'Utilisateur';

            // On sélectionne toutes les actions
            $sql = "SELECT * FROM action";
            $requete = $bdd->query($sql);
            $newsList = $requete->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "ERREUR : " . $e->getMessage();
            exit();
        }
    ?>
    <header class="header_accueil">
    <div class="enssemble">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
        <path fill="#0099ff" fill-opacity="1" d="M0,256L48,245.3C96,235,192,213,288,213.3C384,213,480,235,576,250.7C672,267,768,277,864,277.3C960,277,1056,267,1152,224C1248,181,1344,107,1392,69.3L1440,32L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>
    </svg>

    <div class="enssemble">
        <p style="text-align:center; font-weight: bold; font-size: 1.2em;">Connecté: <?php echo $pseudo; ?></p>
        <h1></h1>
        <nav class="navdeco">
            <ul>
                <li class="lideco"><a href="./new.php">new</a></li>
                <li class="lidecomenu1"><a href="./questionaire.html">Portefeuille</a></li>
            </ul>
        </nav>
    </div>  
    </header>
    
    <?php
         foreach($newsList as $action): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin: 10px;">
                <h2><?php echo htmlspecialchars($action['nom']); ?> (<?php echo htmlspecialchars($action['symbole']); ?>)</h2>
                <p><strong>Prix actuel :</strong> <?php echo number_format($action['price'], 2); ?> €</p>
                <p><strong>ID de l'action :</strong> <?php echo $action['id']; ?></p>
            </div>
        <?php endforeach;
    ?>
    <footer>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#0099ff" fill-opacity="2" d="M0,288L34.3,256C68.6,224,137,160,206,122.7C274.3,85,343,75,411,96C480,117,549,171,617,202.7C685.7,235,754,245,823,245.3C891.4,245,960,235,1029,224C1097.1,213,1166,203,1234,176C1302.9,149,1371,107,1406,85.3L1440,64L1440,320L1405.7,320C1371.4,320,1303,320,1234,320C1165.7,320,1097,320,1029,320C960,320,891,320,823,320C754.3,320,686,320,617,320C548.6,320,480,320,411,320C342.9,320,274,320,206,320C137.1,320,69,320,34,320L0,320Z"></path></svg>
    </footer>
</body>
</html>
