<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>L'Actualités</title>
    <link rel="stylesheet" href="style_graph.css">
    <link rel="website icon" href="photo/logo3.png">
</head>
<body id="body_graph">
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

    $userId = $_SESSION['user_id'];


    try {
        $bdd = new PDO("mysql:host=$servername;dbname=bourse;charset=utf8", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ➤ Récupérer l'identifiant de l'utilisateur connecté
        $user_id = $_SESSION['user_id'];

        $stmt = $bdd->prepare("SELECT pseudo FROM utilisateur WHERE id = :id ");
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $pseudo = $user ? htmlspecialchars($user['pseudo']) : 'Utilisateur non trouvé';

        // On sélectionne une actualité différente aléatoire
        $sql = "SELECT * FROM news ORDER BY RAND() LIMIT 1";
        $stmt1 = $bdd->query($sql);
        $newsList = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $actionId = $newsList[0]['impacted_action_id'];

        $query = "SELECT * FROM action_history WHERE action_id = :actionId";
        $stmt2 = $bdd->prepare($query);
        $stmt2->bindParam(':actionId', $actionId, PDO::PARAM_INT);
        $stmt2->execute();

        // Récupération des résultats
        $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);


        // Conversion des résultats en JSON
        // Conversion des résultats en JSON
        echo "<script>const prices = " . json_encode($results) . ";</script>";
    } catch (PDOException $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
    ?>
    <header class="header_graph">
        <div class="div_header">
            <div class="sniper"></div>
            <h1>Connecté: <?php echo $pseudo; ?></h1>
            <h1 class="logo">BULLFOLIO</h1>
            <nav class="navdecomenu">
                <ul>
                    <li class="lidecomenu1"><a href="./accueil.php" class="lidecomenu2">Accueil</a></li>
                    <li class="lidecomenu1"><a href="./profil_html.php" class="lidecomenu2"">Portefeuille</a></li>
                    <li class="lidecomenu1"><a href="./search_html.php" class="lidecomenu2"">Recherche</a></li>
                    <li class="lidecomenu1" style="background-color:red;"><a href="logout.php" class="lidecomenu2" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">Se déconnecter</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <!-- Partie gauche -->
        <div class="gauche">
            <h1 class="title">📰 Dernières Actualités</h1>
            <?php
            if (!empty($newsList[0])) {
                echo "<h2 class='title2'>" . htmlspecialchars($newsList[0]["title"]) . "</h2>";
                echo "<div class='txt'>" . htmlspecialchars($newsList[0]["text"]) . "</div>";
            } else {
                echo "<p>Aucune actualité disponible.</p>";
            }
            ?>
        </div>

        <!-- Partie droite -->
        <div class="droite">
            <!-- Partie du dessus -->
            <div class="frame info">
                <div class="controls">
                    <button class="control" onclick="viewTotal()">Voir Total</button>
                    <button class="control" onclick="viewLastFiveYears()">Dernières 5 années</button>
                    <button class="control" onclick="viewLastYear()">Dernière année</button>
                </div>
                <canvas id="stockChart" width="800" height="400"></canvas>
                <script>
                    // Récupération du canvas et configuration du contexte
                    const canvas = document.getElementById('stockChart');
                    const ctx = canvas.getContext('2d');

                    const margin = 50;

                    // Fonction pour mapper les prix sur l'axe Y
                    function mapPriceToY(value, minPrice, maxPrice) {
                        return canvas.height - margin - ((value - minPrice) / (maxPrice - minPrice)) * (canvas.height - 2 * margin);
                    }

                    // Fonction pour dessiner les axes
                    function drawAxes(minPrice, maxPrice) {
                        ctx.strokeStyle = 'white';
                        ctx.lineWidth = 1;

                        // Axe Y
                        ctx.beginPath();
                        ctx.moveTo(margin, margin);
                        ctx.lineTo(margin, canvas.height - margin);
                        ctx.stroke();

                        // Axe X
                        ctx.beginPath();
                        ctx.moveTo(margin, canvas.height - margin);
                        ctx.lineTo(canvas.width - margin, canvas.height - margin);
                        ctx.stroke();

                        // Labels Y
                        ctx.fillStyle = 'white';
                        ctx.fillText(maxPrice.toFixed(2), 10, mapPriceToY(maxPrice, minPrice, maxPrice) + 5);
                        ctx.fillText(minPrice.toFixed(2), 10, mapPriceToY(minPrice, minPrice, maxPrice) + 5);
                    }

                    // Fonction pour dessiner le graphique
                    function drawGraph(data) {
                        const minPrice = Math.min(...data.map(p => parseFloat(p.price_at_this_date)));
                        const maxPrice = Math.max(...data.map(p => parseFloat(p.price_at_this_date)));

                        ctx.clearRect(0, 0, canvas.width, canvas.height); // Effacer le canvas
                        drawAxes(minPrice, maxPrice); // Redessiner les axes

                        for (let i = 1; i < data.length; i++) {
                            const x1 = margin + ((i - 1) * (canvas.width - 2 * margin)) / (data.length - 1);
                            const y1 = mapPriceToY(parseFloat(data[i - 1].price_at_this_date), minPrice, maxPrice);
                            const x2 = margin + (i * (canvas.width - 2 * margin)) / (data.length - 1);
                            const y2 = mapPriceToY(parseFloat(data[i].price_at_this_date), minPrice, maxPrice);

                            // Ligne verte si montée, rouge si descente
                            ctx.strokeStyle = parseFloat(data[i].price_at_this_date) > parseFloat(data[i - 1].price_at_this_date) ? 'green' : 'red';
                            ctx.lineWidth = 2;
                            ctx.beginPath();
                            ctx.moveTo(x1, y1);
                            ctx.lineTo(x2, y2);
                            ctx.stroke();
                        }
                    }

                    // Zoom : Voir toutes les données
                    function viewTotal() {
                        drawGraph(prices);
                    }

                    // Zoom : Voir les 5 dernières années
                    function viewLastFiveYears() {
                        const lastFiveYears = prices.filter(p => p.date <= prices[59].date);
                        drawGraph(lastFiveYears);
                    }

                    // Zoom : Voir la dernière année
                    function viewLastYear() {
                        const lastYear = prices.filter(p => p.date >= prices[11].date);
                        drawGraph(lastYear);
                    }

                    // Dessiner le graphique initial (Total)
                    viewTotal();

                </script>
            </div>
            <!-- Partie du dessous -->
            <div class="dessous">
                <?php $query_frame = "SELECT symbole, price FROM action WHERE id = :actionId";
                $stmt3 = $bdd->prepare($query_frame);
                $stmt3->bindParam(':actionId', $actionId, PDO::PARAM_INT);
                $stmt3->execute();
                $action = $stmt3->fetch(); ?>
                <div class="frame f2">
                    <h2><?php echo $action['symbole'];?></h2>
                </div>
                <div class="frame f2">
                    <h2>ACT<br><?php echo $action['price'];?></h2>
                </div>
                <?php
                $maxPrice = null;
                $minPrice = 9999999999999;
                foreach ($results as $row) {
                    if (is_null($maxPrice) || $row['price_at_this_date'] > $maxPrice) {
                        $maxPrice = $row['price_at_this_date'];
                    }
                    if (is_null($minPrice) || $row['price_at_this_date'] < $minPrice) {
                        $minPrice = $row['price_at_this_date'];
                    }
                }?>
                <div class="frame f2"><h2>MAX<br><?php echo $maxPrice;?></h2></div>
                <div class="frame f2"><h2>MIN<br><?php echo $maxPrice;?></h2></div>
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