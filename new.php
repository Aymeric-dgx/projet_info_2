<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>L'Actualités</title>
        <link rel="stylesheet" href="style.css">
        <link rel="website icon" href="photo/logo.png">
    </head>
<body id="body3">
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

            // On sélectionne 2 actualités différentes aléatoires
            $sql = "SELECT * FROM news ORDER BY RAND() LIMIT 2";
            $requete = $bdd->query($sql);
            $newsList = $requete->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "ERREUR : " . $e->getMessage();
            exit();
        }
    ?>
    <header class="header1">
        <div class="enssemble">
        <p style="text-align:center; font-weight: bold; font-size: 1.2em;">Connecté: <?php echo $pseudo; ?></p>
            <h1 style="color:black;">NEWS</h1>
            <nav class="navdeco">
                <ul>
                    <li class="lideco"><a  href="./accueil.php" >Accueil</a></li>
                    <li class="lidecomenu1"><a href="./questionaire.html" >Portefeuille</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
    <div class="container">
        <!-- Partie gauche -->
        <div class="gauche">
        <h1 class="news-title">📰 Dernières Actualités</h1>
            <div class="colonne">
                <div class="bloc">
                    <?php
                    if (!empty($newsList[0])) {
                        echo "<h2>" . htmlspecialchars($newsList[0]["titre"]) . "</h2>";
                        echo "<p>" . htmlspecialchars($newsList[0]["blabla"]) . "</p>";
                    } else {
                        echo "<p>Aucune actualité disponible.</p>";
                    }
                    ?>
                </div>
                <div class="bloc"><h1 class="news-title">📰 INformation complémentaire</h1>
                <?php
                    if (!empty($newsList[1])) {
                        echo "<h2>" . htmlspecialchars($newsList[1]["titre"]) . "</h2>";
                        echo "<p>" . htmlspecialchars($newsList[1]["blabla"]) . "</p>";
                    } else {
                        echo "<p>Aucune actualité disponible.</p>";
                    }
                ?>
                </div>
            </div>
        </div>

        <!-- Partie droite -->
        <div class="droite">
            <h2>Titre à droite</h2>
        </div>
    </div>
    </main>
    <footer class="footer">
    <div class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Simulateur Boursier</p>
        <p>Réalisé par Mathéo, Aymeric et Nathan</p>
    </div>
</footer>

</body>
</html>
