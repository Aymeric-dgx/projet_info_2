<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="website icon" href="photo/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="body_accueil">
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


} catch (PDOException $e) {
    echo "ERREUR : " . $e->getMessage();
    exit();
}

// ➤ Récupérer l'identifiant de l'utilisateur connecté
$userId = $_SESSION['user_id'];
$email = $_SESSION['email'];

$stmt = $bdd->prepare("SELECT pseudo, solde FROM utilisateur WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$pseudo = $user ? htmlspecialchars($user['pseudo']) : 'Utilisateur non trouvé';
// récuperer le prix total des actions possédé

$recup= $bdd->prepare("SELECT SUM(average_price*quantity) AS somme FROM global_wallet WHERE id_user=:id ");
$recup->bindParam(':id',$userId,PDO::PARAM_INT);
$recup->execute();
$somme=$recup->fetch(PDO::FETCH_ASSOC);
$total = $somme["somme"];

// Déterminer le critère de tri
$tri = $_GET['tri'] ?? 'nom';

// Construire la requête SQL - IMPORTANT: utilisez les bons noms de colonnes
$sql = "";

switch($tri) {
    case 'prix':
        $sql .= "SELECT * FROM action ORDER BY price ASC"; // Changé 'prix' en 'price' pour correspondre à votre affichage
        break;
    case 'progression_1m':
        $sql .= "SELECT a.*, a1.action_id, a1.date, 
       (a2.price_at_this_date / a1.price_at_this_date) AS ratio
FROM action a
JOIN action_history a1 ON a.id = a1.action_id
JOIN (
    SELECT action_id, price_at_this_date, date 
    FROM action_history 
    WHERE date = (SELECT MAX(date) FROM action_history)
) a2 ON a1.action_id = a2.action_id
WHERE a1.date = (
    SELECT date FROM action_history 
    WHERE date <= DATE_SUB((SELECT MAX(date) FROM action_history), INTERVAL 1 MONTH) 
    ORDER BY date DESC LIMIT 1
)
GROUP BY a1.action_id
ORDER BY ratio DESC;

";
        break;
    case 'progression_an':
        $sql .= "SELECT a.*, a1.action_id,
       (a2.price_at_this_date / a1.price_at_this_date) AS ratio
FROM action a
JOIN action_history a1 ON a.id = a1.action_id
JOIN (
    SELECT action_id, price_at_this_date, date 
    FROM action_history 
    WHERE date = (SELECT MAX(date) FROM action_history)
) a2 ON a1.action_id = a2.action_id
WHERE a1.date = (
    SELECT date FROM action_history 
    WHERE date <= DATE_SUB((SELECT MAX(date) FROM action_history), INTERVAL 12 MONTH) 
    ORDER BY date DESC LIMIT 1
)
GROUP BY a1.action_id
ORDER BY ratio DESC;
";

        break;
    default:
        $sql .= "SELECT * FROM action ORDER BY nom ASC";
}

$stmt = $bdd->query($sql);
$resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pour les boutons acheter et vendre

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $price = $_POST['price'];
    $id_user = $_SESSION['user_id'];
    $action_type = $_POST['action_type'];
    $id_action = $_POST['id_action'];
    $quantity = $_POST['quantity'];

    // Vérifier si l'utilisateur possède déjà cette action
    $stmt = $bdd->prepare("SELECT * FROM global_wallet WHERE id_user = :id_user AND id_action = :id_action");
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':id_action', $id_action, PDO::PARAM_INT);
    $stmt->execute();
    $existing = $stmt->fetch();

    // Récupération du solde actuel de l'utilisateur
    $stmt = $bdd->prepare("SELECT solde FROM utilisateur WHERE id = :id_user");
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($action_type === 'acheter') {
        $solde = $user['solde'];
        $total_cost = $price * $quantity;

        if ($total_cost <= $solde) {
            // Déduire le prix total du solde
            $stmt = $bdd->prepare("UPDATE utilisateur SET solde = solde - :total_cost WHERE id = :id_user");
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':total_cost', $total_cost, PDO::PARAM_INT);
            $stmt->execute();

            if ($existing) {
                // Mise à jour si l'action existe déjà
                $new_quantity = $existing['quantity'] + $quantity;
                $new_avg = (($existing['average_price'] * $existing['quantity']) + $price * $quantity) / $new_quantity;

                $update = $bdd->prepare("UPDATE global_wallet SET quantity = ?, average_price = ? WHERE id = ?");
                $update->execute([$new_quantity, $new_avg, $existing['id']]);
            } else {
                // Insertion si nouvelle action
                $insert = $bdd->prepare("INSERT INTO global_wallet (id_user, id_action, quantity, average_price) VALUES (?, ?, ?, ?)");
                $insert->execute([$id_user, $id_action, $quantity, $price]);
            }
        } else {
            echo "Solde insuffisant pour effectuer cet achat.";
        }
    }
    elseif ($action_type === 'vendre') {
        if ($existing && $existing['quantity'] >= $quantity) {
            $total_cost = $price * $quantity;

            $stmt = $bdd->prepare("UPDATE utilisateur SET solde = solde + :total_cost WHERE id = :id_user");
            $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':total_cost', $total_cost, PDO::PARAM_STR);
            $stmt->execute();

            $new_quantity = $existing['quantity'] - $quantity;

            if ($new_quantity > 0) {
                $update = $bdd->prepare("UPDATE global_wallet SET quantity = ? WHERE id = ?");
                $update->execute([$new_quantity, $existing['id']]);
            } else {
                $delete = $bdd->prepare("DELETE FROM global_wallet WHERE id = ?");
                $delete->execute([$existing['id']]);
            }

            // Message de succès
            echo "<script>alert('Vente effectuée avec succès!'); window.location.href='accueil.php';</script>";
        }else {
            // Popup d'erreur si l'utilisateur ne possède pas l'action
            echo "<script>alert('Erreur : Vous ne possédez pas cette action ou votre quantité est insuffisante.'); window.location.href='accueil.php';</script>";
            exit();
        }
    }
    else {
        echo "Aucun bouton valide n'a été cliqué";
    }
}
?>
<header class="header_accueil">
    <div class="enssemble">
        <div class="sniper"></div>
        <h1 >Connecté: <?php echo $pseudo; ?></h1>
        <h1 class="logo">BullFolio</h1>
        <nav class="navdeco">
            <ul>
                <li class="lideco"><a href="./new.php" style="color: white;">news</a></li>
                <li class="lidecomenu1"><a href="./profil_html.php" style="color: white;">Portefeuille</a></li>
                <li class="lidecomenu1"><a href="./search_html.php" style="color: white;">Recherche</a></li>
                <li class="lidecomenu1" style="background-color:red;"><a href="logout.php" style="color: white;" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">Se déconnecter</a></li>
            </ul>
        </nav>
    </div>
</header>
<main class="teste">
    <br>
    <div class="info_accueil">
        <h1 class="game-date"><?php echo date('d/m/Y'); ?></h1>
        <div  style="display:flex;flex-direction: column;gap:0px;">
            <h1 class="game-date">Liquide: <?php echo number_format($user['solde'], 2, ',', ' '); ?> €</h1>
            <h1 class="game-date"> Action: <?php echo number_format($total, 2, ',', ' '); ?> €</h1>
            <h1 class="game-date">Solde cummulé: <?php echo number_format($total+$user['solde'], 2, ',', ' '); ?> €</h1>
        </div>

    </div>

    <form method="GET" action="" id="filterForm">
        <div class="radio-container">
            <div class="radio-input">
                <label>
                    <input type="radio" name="tri" value="nom" <?= (empty($_GET['tri']) || $_GET['tri'] === 'nom' ? 'checked' : '') ?>>
                    <span>nom</span>
                </label>
                <label>
                    <input type="radio" name="tri" value="prix" <?= (isset($_GET['tri']) && $_GET['tri'] === 'prix' ? 'checked' : '') ?>>
                    <span>prix</span>
                </label>
                <label>
                    <input type="radio" name="tri" value="progression_1m" <?= (isset($_GET['tri']) && $_GET['tri'] === 'progression_1m' ? 'checked' : '') ?>>
                    <span>progression 1 mois</span>
                </label>
                <label>
                    <input type="radio" name="tri" value="progression_an" <?= (isset($_GET['tri']) && $_GET['tri'] === 'progression_an' ? 'checked' : '') ?>>
                    <span>progression 1 an</span>
                </label>
                <span class="selection"></span>
            </div>
        </div>
    </form>
    <div class="flex_action">
        <?php
        foreach($resultats as $action): ?>
            <?php
            // Requête pour récupérer combien l'utilisateur possède cette action
            $stmtQuantite = $bdd->prepare("SELECT quantity FROM global_wallet WHERE id_user = :id_user AND id_action = :id_action");
            $stmtQuantite->bindParam(':id_user', $userId, PDO::PARAM_INT);
            $stmtQuantite->bindParam(':id_action', $action['id'], PDO::PARAM_INT);
            $stmtQuantite->execute();
            $walletEntry = $stmtQuantite->fetch(PDO::FETCH_ASSOC);

            $quantitePossedee = $walletEntry ? $walletEntry['quantity'] : 0;
            ?>
            <div class="card">
                <div class="flex_card">
                    <a href="info.php?id=<?php echo $action['id']; ?>" ><!--- nathannnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn--->
                        <button type="card-button" class="info-button">Info</button>
                        <?php
                        $_SESSION['action_id'] = $action['id'];
                        ?>
                    </a>
                    <p class="azi"> Possédé :&nbsp;<strong><?php echo $quantitePossedee; ?></strong> </p>
                </div>
                <h1 class="heading " >(<?php echo htmlspecialchars($action['symbole']); ?>)</h1>
                <h2 class="heading "><?php echo htmlspecialchars($action['nom']);  ?> </h2>
                <p class="heading "><strong>Prix actuel :</strong> <?php echo number_format($action['price'], 2); ?> €</p>
                <form method="POST">
                    <!-- Champ caché pour identifier l'action -->
                    <input type="hidden" name="id_action" value="<?php echo $action['id']; ?>">
                    <input type="hidden" name="price" value="<?php echo $action['price']; ?>">

                    <div class="flex_button">
                        <!-- Bouton Acheter -->
                        <button type="submit" class="acheter" name="action_type" value="acheter">Acheter <span></span></button>

                        <!-- Bouton Vendre -->
                        <button type="submit" class="vendre" name="action_type" value="vendre">Vendre <span></span></button>
                    </div>
                    <br>
                    <div class="container">
                        <input type="number" class="input" id="quantity" name="quantity" min="1"  required>
                        <label for="quantity" class="label">Quantité </label>
                    </div>


                </form>

            </div>

        <?php endforeach;
        ?>

    </div><br>

</main>

<div class="middle">
    <br>
    <!-- From Uiverse.io by vikas7754 -->
    <div class="truck">
        <div class="truck__body">
            <div class="truck__body truck__body--top">
                <div class="truck__window">
                    <div class="truck__window-glass"></div>
                </div>
            </div>
            <div class="truck__body truck__body--mid">
                <div class="truck__mid-body"></div>
            </div>
            <div class="truck__body truck__body--bottom">
                <div class="truck__underpanel"></div>
                <div class="truck__rear-bumper"></div>
                <div class="truck__side-skirt"></div>
            </div>
        </div>
        <div class="truck__wheel truck__wheel--front">
            <div class="truck__wheel-arch"></div>
            <div class="truck__wheel-arch-trim truck__wheel-arch-trim--top"></div>
            <div class="truck__wheel-arch-trim truck__wheel-arch-trim--left"></div>
            <div class="truck__wheel-arch-trim truck__wheel-arch-trim--right"></div>
            <div class="truck-wheel">
                <div class="truck-wheel__rim">
                    <div style="--index: 0;" class="truck-wheel__spoke"></div>
                    <div style="--index: 1;" class="truck-wheel__spoke"></div>
                    <div style="--index: 2;" class="truck-wheel__spoke"></div>
                    <div style="--index: 3;" class="truck-wheel__spoke"></div>
                    <div style="--index: 4;" class="truck-wheel__spoke"></div>
                    <div style="--index: 5;" class="truck-wheel__spoke"></div>
                    <div style="--index: 6;" class="truck-wheel__spoke"></div>
                </div>
            </div>
        </div>
        <div class="truck__wheel truck__wheel--rear">
            <div class="truck__wheel-arch"></div>
            <div class="truck__wheel-arch-trim truck__wheel-arch-trim--top"></div>
            <div class="truck__wheel-arch-trim truck__wheel-arch-trim--left"></div>
            <div class="truck__wheel-arch-trim truck__wheel-arch-trim--right"></div>
            <div class="truck-wheel">
                <div class="truck-wheel__rim">
                    <div style="--index: 0;" class="truck-wheel__spoke"></div>
                    <div style="--index: 1;" class="truck-wheel__spoke"></div>
                    <div style="--index: 2;" class="truck-wheel__spoke"></div>
                    <div style="--index: 3;" class="truck-wheel__spoke"></div>
                    <div style="--index: 4;" class="truck-wheel__spoke"></div>
                    <div style="--index: 5;" class="truck-wheel__spoke"></div>
                    <div style="--index: 6;" class="truck-wheel__spoke"></div>
                </div>
            </div>
        </div>
        <div class="truck__headlight"></div>
        <div class="truck__taillight"></div>
        <div class="truck__indicator"></div>
        <div class="truck__foglight"></div>
    </div>

    <div class="cyber-text-container">
        <h2 class="cyber-title">⏱️ Simulation Temporelle Accélérée</h2>

        <p class="cyber-line">
            <span class="cyber-bracket">[</span>SYSTEME<span class="cyber-bracket">]</span>
            Le temps s'écoule différemment sur notre plateforme.
        </p>

        <p class="cyber-highlight">
            1 mois boursier = 2 minutes réelles
        </p>

        <p class="cyber-line">
            <span class="cyber-bracket">[</span>AVANTAGE<span class="cyber-bracket">]</span>
            Testez vos stratégies en un temps record et optimisez vos performances.
        </p>

        <div class="cyber-divider"></div>

        <p class="cyber-cta">
            Prêt à <span class="cyber-glow">décoller</span> ?
        </p>
    </div>
    <br>
</div>
<div class="top_flop">
    <script>
        function graph(prices, stockchart) {
            // Récupération du canvas et configuration du contexte
            const canvas = document.getElementById(stockchart);
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

            // Zoom : Voir la dernière année
            function viewLastYear() {
                const oneYearAgo = new Date();
                oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);

                // Filtrer les données des 12 derniers mois
                const lastYear = prices.filter(p => new Date(p.date) >= oneYearAgo);

                // Limiter à 12 éléments
                const limitedData = lastYear.slice(0, 12);

                drawGraph(limitedData);
            }






            // Dessiner le graphique 12 dernier mois
            viewLastYear();
        }
    </script>
    <section class="graph_acceuil top">
        <?php
        try{
            $query1 =  "SELECT a1.action_id, 
       (a2.price_at_this_date / a1.price_at_this_date) AS ratio
FROM action_history a1
JOIN action_history a2 ON a1.action_id = a2.action_id
WHERE a1.date = (
    SELECT date FROM action_history 
    WHERE date <= DATE_SUB((SELECT MAX(date) FROM action_history), INTERVAL 12 MONTH) 
    ORDER BY date DESC LIMIT 1
)
AND a2.date = (SELECT MAX(date) FROM action_history)
ORDER BY ratio DESC
LIMIT 1;
";
            $stmt1 = $bdd->prepare($query1);
            $stmt1->execute();

            $actionId1 = $stmt1->fetch(PDO::FETCH_ASSOC)['action_id'];

            $query = "SELECT symbole FROM action WHERE id = :actionId1";
            $stmt5 = $bdd->prepare($query);
            $stmt5->bindParam(':actionId1', $actionId1, PDO::PARAM_INT);
            $stmt5->execute();

            $action_sym = $stmt5->fetch(PDO::FETCH_ASSOC)['symbole'];

            $query2 = "SELECT * FROM action_history WHERE action_id = :actionId1 ORDER BY date ASC";
            $stmt2 = $bdd->prepare($query2);
            $stmt2->bindParam(':actionId1', $actionId1, PDO::PARAM_INT);
            $stmt2->execute();

            // Récupération des résultats
            $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

            // Conversion des résultats en JSON
            echo "<script>const prices_top = " . json_encode($results) . ";</script>";
        } catch (PDOException $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
            <h2>Top Performances</h2>
        <div class="frame">
            <canvas id="stockChartTop" width="800" height="400"></canvas>
            <script>graph(prices_top, "stockChartTop")</script>
            <p><?php try {
                    echo $action_sym;
                } catch (Throwable $e) {
                    echo "none";
                } ?></p>
        </div>
    </section>
    <section class="graph_acceuil flop">
        <?php
        try{
            $query3 =  "SELECT a1.action_id, 
       (a2.price_at_this_date / a1.price_at_this_date) AS ratio
FROM action_history a1
JOIN action_history a2 ON a1.action_id = a2.action_id
WHERE a1.date = (
    SELECT date FROM action_history 
    WHERE date <= DATE_SUB((SELECT MAX(date) FROM action_history), INTERVAL 12 MONTH) 
    ORDER BY date DESC LIMIT 1
)
AND a2.date = (SELECT MAX(date) FROM action_history)
ORDER BY ratio ASC
LIMIT 1;
";
            $stmt3 = $bdd->prepare($query3);
            $stmt3->execute();

            $actionId2 = $stmt3->fetch(PDO::FETCH_ASSOC)['action_id'];


            $query4 = "SELECT * FROM action_history WHERE action_id = :actionId2 ORDER BY date ASC";
            $stmt4 = $bdd->prepare($query4);
            $stmt4->bindParam(':actionId2', $actionId2, PDO::PARAM_INT);
            $stmt4->execute();

            // Récupération des résultats
            $results2 = $stmt4->fetchAll(PDO::FETCH_ASSOC);

            $query6 = "SELECT symbole FROM action WHERE id = :actionId2";
            $stmt6 = $bdd->prepare($query6);
            $stmt6->bindParam(':actionId2', $actionId2, PDO::PARAM_INT);
            $stmt6->execute();

            $action_sym2 = $stmt6->fetch(PDO::FETCH_ASSOC)['symbole'];


            // Conversion des résultats en JSON
            echo "<script>const prices_flop = " . json_encode($results2) . ";</script>";
        } catch (PDOException $e) {
            echo "<p>Erreur : " . $e->getMessage() . "</p>";
        }
        ?>
        <h2>Flop Performances</h2>
        <div class="frame">
            <canvas id="stockChartFlop" width="800" height="400"></canvas>
            <script>graph(prices_flop, "stockChartFlop")</script>
            <p><?php echo @$action_sym2;?>
                </p>
        </div>
    </section>
</div>


<footer class="footer">

    <div>
        <p>&copy; <?php echo date("Y"); ?> Simulateur Boursier</p>
        <p>Réalisé par Mathéo, Aymeric et Nathan</p>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('input[type="radio"][name="tri"]');

        // Soumettre automatiquement le formulaire quand un radio est cliqué
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    });
</script>
</body>
</html>