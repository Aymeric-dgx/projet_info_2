<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="website icon" href="photo/logo.png">
    <title>Portefeuille</title>
    <script>
        if('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service_worker.js')
        }
    </script>
    <link rel="stylesheet" href="profil_css.css">
    <?php session_start();
    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        // S'il n'est pas connecté, on le redirige vers la page de login
        header("Location: index.php");
        exit();
    }
    ?>
</head>

<body>
<header class="header_accueil">
    <div class="enssemble">
        <div class="sniper"></div>
        <h1 >Connecté: <?php echo $_SESSION['pseudo']; ?></h1>
        <h1 class="logo">BullFolio</h1>
        <nav class="navdeco">
            <ul>
                <li class="lideco"><a href="./accueil.php" style="color: white;">Accueil</a></li>
                <li class="lidecomenu1"><a href="./new.php" style="color: white;">news</a></li>
                <li class="lidecomenu1"><a href="./search_html.php" style="color: white;">Recherche</a></li>
                <li class="lidecomenu1" style="background-color:red;"><a href="logout.php" style="color: white;" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">Se déconnecter</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main_style">
    <div class="left-right">
    <div class="left_column">
        <div class="top_box">
            <div>
                <h1><?php echo $_SESSION['pseudo']; ?></h1>
                <h3><?php echo $_SESSION['email']; ?></h3>
            </div>
            <div>
                <h2>Solde : <?php include 'scripts/get_solde.php'; ?> </h2>
                <h2>Valeur totale : <?php include 'scripts/get_total_value.php'; ?> </h2>
            </div>
        </div>
            <div class="follow_container">
                <div class="sub_follow_container">
                    <h2 class="title_container_actions">Followers</h2>
                    <table class="data_table">
                        <thead>
                        <tr>
                            <th>Pseudo</th>
                            <th>Email</th>
                            <th>Solde</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php include 'scripts/get_followers.php'; ?>
                        <?php foreach ($followers as $follower): ?>
                            <tr>
                                <td><?php echo $follower['pseudo']; ?></td>
                                <td><?php echo $follower['email']; ?></td>
                                <td><?php echo $follower['solde']; ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="sub_follow_container">
                    <h2 class="title_container_actions">Follows</h2>
                    <table class="data_table">
                        <thead>
                        <tr>
                            <th>Pseudo</th>
                            <th>Email</th>
                            <th>Solde</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php include 'scripts/get_follows.php'; ?>
                        <?php foreach ($follows as $follow): ?>
                            <tr>
                                <td><?php echo $follow['pseudo']; ?></td>
                                <td><?php echo $follow['email']; ?></td>
                                <td><?php echo $follow['solde']; ?> €</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


    <div class="right_column">
        <h1 class="title_container_actions">Actions possédées</h1>
        <div class="scrollable_window">
            <?php include 'scripts/script_show_action_possesed.php'; ?>
            <div class="actions_container">
                <?php foreach ($actions as $action): ?>
                    <div class="action_cube">
                        <h4><?php echo $action['nom'],' (',$action['symbole'], ')'; ?></h4>
                        <p>Quantité : <?php echo $action['quantity']; ?></p>
                        <p>Prix : <?php echo $action['price']; ?> €</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    </div>
    <div class="bottom_box">
        <div>
            <script>
                function graph(prices, stockchart) {
                    // Récupération du canvas et configuration du contexte
                    const canvas = document.getElementById(stockchart);
                    const ctx = canvas.getContext('2d');

                    const margin = 50;

                    // Fonction pour mapper les prix sur l'axe Y
                    function mapPriceToY(value1, minPrice, maxPrice) {
                        return canvas.height - margin - ((value1 - minPrice) / (maxPrice - minPrice)) * (canvas.height - 2 * margin);
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
                        const minPrice = Math.min(...data.map(p => parseFloat(p.value)));
                        const maxPrice = Math.max(...data.map(p => parseFloat(p.value)));

                        ctx.clearRect(0, 0, canvas.width, canvas.height); // Effacer le canvas
                        drawAxes(minPrice, maxPrice); // Redessiner les axes

                        for (let i = 1; i < data.length; i++) {
                            const x1 = margin + ((i - 1) * (canvas.width - 2 * margin)) / (data.length - 1);
                            const y1 = mapPriceToY(parseFloat(data[i - 1].value), minPrice, maxPrice);
                            const x2 = margin + (i * (canvas.width - 2 * margin)) / (data.length - 1);
                            const y2 = mapPriceToY(parseFloat(data[i].value), minPrice, maxPrice);

                            // Ligne verte si montée, rouge si descente
                            ctx.strokeStyle = parseFloat(data[i].value) > parseFloat(data[i - 1].value) ? 'green' : 'red';
                            ctx.lineWidth = 2;
                            ctx.beginPath();
                            ctx.moveTo(x1, y1);
                            ctx.lineTo(x2, y2);
                            ctx.stroke();
                        }
                    }


                    // Zoom : Voir la dernière année
                    // Zoom : Voir la dernière année
                    function viewLastYear() {
                        const oneYearAgo = new Date();
                        oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);

                        // Filtrer les données des 12 derniers mois
                        const lastYear = prices.filter(p => new Date(p.registement_date) >= oneYearAgo);

                        // Limiter à 12 éléments
                        const limitedData = lastYear.slice(0, 12);

                        drawGraph(limitedData);
                    }

                    // Dessiner le graphique 12 dernier mois
                    viewLastYear();
                }
            </script>
            <div class="bottom">
            <div class="frame">
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";

                $userId = $_SESSION['user_id'];

                $bdd = new PDO("mysql:host=$servername;dbname=bourse;charset=utf8", $username, $password);
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                try{
                    $query = "SELECT * FROM (
    SELECT * FROM solde_history 
    WHERE id_user = :user_id 
    ORDER BY registement_date DESC 
    LIMIT 12
) AS recent_data
ORDER BY registement_date ASC;
";
                    $stmt = $bdd-> prepare($query);
                    $stmt -> bindParam(":user_id", $userId, PDO::PARAM_INT);
                    $stmt->execute();

                    // Récupération des résultats
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


                    // Conversion des résultats en JSON
                    // Conversion des résultats en JSON
                    echo "<script>const prices_solde = " . json_encode($results) . ";</script>";
                } catch (PDOException $e) {
                    echo "<p>Erreur : " . $e->getMessage() . "</p>";}


                ?>
                <canvas id="stockChartSolde" width="800" height="400"></canvas>
                <script>graph(prices_solde, "stockChartSolde")</script>
                <h3>Solde</h3>
            </div>
            <div class="frame">
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";

                $userId = $_SESSION['user_id'];

                $bdd = new PDO("mysql:host=$servername;dbname=bourse;charset=utf8", $username, $password);
                $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                try{
                    $query = "SELECT * FROM (
    SELECT * FROM total_value_history 
    WHERE id_user = :user_id 
    ORDER BY registement_date DESC 
    LIMIT 12
) AS recent_data
ORDER BY registement_date ASC;
";
                    $stmt = $bdd-> prepare($query);
                    $stmt -> bindParam(":user_id", $userId, PDO::PARAM_INT);
                    $stmt->execute();

                    // Récupération des résultats
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


                    // Conversion des résultats en JSON
                    // Conversion des résultats en JSON
                    echo "<script>const prices_total_value = " . json_encode($results) . ";</script>";
                } catch (PDOException $e) {
                    echo "<p>Erreur : " . $e->getMessage() . "</p>";}


                ?>
                <canvas id="stockChartTotalvalue" width="800" height="400"></canvas>
                <script>graph(prices_total_value, "stockChartTotalvalue")</script>
                <h3>Valeur total</h3>
            </div>
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