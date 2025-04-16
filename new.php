<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>L'Actualités</title>
    <link rel="stylesheet" href="style_new.css">
    <link rel="website icon" href="photo/logo3.png">
</head>
<body id="body_new">
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
        $user_id = $_SESSION['user_id'];
        $stmt = $bdd->prepare("SELECT pseudo FROM utilisateur WHERE pseudo = :identifiant ");
        $stmt->bindParam(':identifiant', $identifiant, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $identifiant = $user ? htmlspecialchars($user['identifiant']) : 'Utilisateur';

        // On sélectionne 2 actualités différentes aléatoires
        $sql = "SELECT * FROM news ORDER BY RAND() LIMIT 1";
        $requete = $bdd->query($sql);
        $newsList = $requete->fetchAll(PDO::FETCH_ASSOC);


    $actionId = 6;
    $query = "SELECT date, price_at_this_date FROM action_history WHERE action_id = :actionId";
    $stmt = $bdd->prepare($query);
    $stmt->bindParam(':actionId', $actionId, PDO::PARAM_INT);
    $stmt->execute();

    //Même chose, mais trier dans l'odre descendant
    $query2 = "SELECT date, price_at_this_date FROM action_history WHERE action_id = :actionId ORDER BY date DESC";
    $stmt2 = $bdd->prepare($query2);
    $stmt2->bindParam(':actionId', $actionId, PDO::PARAM_INT);
    $stmt2->execute();

    // Récupération des résultats
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Récupération des résultats dans l'ordre descendant
    $results_desc = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Conversion des résultats en JSON
    echo "<script>const prices = " . json_encode($results) . ";</script>";
    //même chose pour les resultats descendant
    echo "<script>const prices_desc = " . json_encode($results_desc) . ";</script>";
    } catch (PDOException $e) {
    echo "<p>Erreur : " . $e->getMessage() . "</p>";



    } catch (PDOException $e) {
        echo "ERREUR : " . $e->getMessage();
        exit();
    }
    ?>
    <header class="header_new">
        <div class="div_header">
            <div class="sniper"></div>
            <h1>Connecté: <?php echo $identifiant; ?></h1>
            <h1>NEWS</h1>
            <nav class="navdecomenu">
                <ul>
                    <li class="lidecomenu1"><a href="./accueil.php" class="lidecomenu2">Accueil</a></li>
                    <li class="lidecomenu1"><a href="./temp.php" class="lidecomenu2">Portefeuille</a></li>
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
                        echo "<h2>" . htmlspecialchars($newsList[0]["title"]) . "</h2>";
                        echo "<p>" . htmlspecialchars($newsList[0]["text"]) . "</p>";
                    } else {
                        echo "<p>Aucune actualité disponible.</p>";
                    }
                    ?>
        </div>
        <!-- Partie droite -->
        <div class="droite">
            <div class="controls">
                <button class="control" onclick="viewTotal()">Voir Total</button>
                <button class="control" onclick="viewLastFiveYears()">Dernières 5 années</button>
                <button class="control" onclick="viewLastYear()">Dernière année</button>
            </div>
            <canvas class="new_graph" id="stockChart" width="800" height="400"></canvas>
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
                    const lastFiveYears = prices.filter(p => p.date >= prices_desc[59].date);
                    drawGraph(lastFiveYears);
                }

                // Zoom : Voir la dernière année
                function viewLastYear() {
                    const lastYear = prices.filter(p => p.date >= prices_desc[11].date);
                    drawGraph(lastYear);
                }

                // Dessiner le graphique initial (Total)
                viewTotal();

            </script>
        </div>
        <!-- Partie du dessous -->
        <div>
            
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
