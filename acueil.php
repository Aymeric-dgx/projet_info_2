<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css">
    <link rel="website icon" href="photo/logo.png">
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
        $stmt = $bdd->prepare("SELECT pseudo FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $pseudo = $user ? htmlspecialchars($user['pseudo']) : 'Utilisateur non trouvé';

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
    ?>
    <header class="header_accueil">
        <div class="enssemble">
            <div class="sniper"></div>
            <h1 >Connecté: <?php echo $pseudo; ?></h1>
            <h1 class="logo">Trade</h1>
            <nav class="navdeco">
                <ul>
                    <li class="lideco"><a href="./new.php" style="color: white;">new</a></li>
                    <li class="lidecomenu1"><a href="./questionaire.html" style="color: white;">Portefeuille</a></li>
                    <li class="lidecomenu1"><a href="./questionaire.html" style="color: white;">Recherche</a></li>
                </ul>
            </nav>
        </div>  
    </header>
    <main class="teste">
        <br>
        <div style="margin-left: 10px;"> <h1 class="game-date"> <?php echo date('d/m/Y'); ?></h1></div>
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
                <div class="card">
                <a href="info.php?id=<?php echo $action['id']; ?>" ><!--- nathannnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn--->
                            <button type="card-button" class="info-button">Info</button>
                    </a>
                    
                        <h1 class="heading " >(<?php echo htmlspecialchars($action['symbole']); ?>)</h1>
                        <h2 class="heading "><?php echo htmlspecialchars($action['nom']);  ?> </h2>
                        <p class="heading "><strong>Prix actuel :</strong> <?php echo number_format($action['price'], 2); ?> €</p>
                    
                    <form action="">
                            <!-- Champ caché pour identifier l'action -->
                            <input type="hidden" name="id_action" value="<?php echo $action['id']; ?>">

                            <div class="flex_button">
                                <!-- Bouton Acheter -->
                                <button type="submit" class="acheter" name="action" value="acheter">Acheter <span></span></button> 

                                <!-- Bouton Vendre -->
                                <button type="submit" class="vendre" name="action" value="vendre">Vendre <span></span></button>
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
    
    <div class="container">
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
