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
        $sql = "SELECT * FROM action ORDER BY ";

        switch($tri) {
            case 'prix':
                $sql .= "price ASC"; // Changé 'prix' en 'price' pour correspondre à votre affichage
                break;
            case 'progression_1m':
                $sql .= "progression_1mois DESC";
                break;
            case 'progression_an':
                $sql .= "progression_1an DESC";
                break;
            default:
                $sql .= "nom ASC";
        }

        $stmt = $bdd->query($sql);
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            // Pour les bouton acheter et vendre 

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
                        </a>
                        <p class="azi"> Possédé : <strong><?php echo $quantitePossedee; ?></strong> </p>
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
    <div class="graphique">
        <!-- Partie gauche -->
        <div class="gauche_action">
        <h1 class="news-title_action"> Top</h1>
        </div>

        <!-- Partie droite -->
        <div class="droite_action">
        <h1 class="news-title_action"> Flop</h1>
    </div>
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