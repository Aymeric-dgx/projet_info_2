<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="style.css">
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
        $stmt = $bdd->prepare("SELECT pseudo FROM utilisateur WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $pseudo = $user ? htmlspecialchars($user['pseudo']) : 'Utilisateur';

        // On sélectionne toutes les actions
        $sql = "SELECT * FROM action";
        $requete = $bdd->query($sql);
        $newsList = $requete->fetchAll(PDO::FETCH_ASSOC);


        // Récupère le filtre sélectionné depuis le formulaire
        $filtre = isset($_GET['filtre']) ? $_GET['filtre'] : 'rien'; // Valeur par défaut si rien n'est sélectionné

        // Fonction pour filtrer par nom
        if ($filtre === 'nom') {
            usort($newsList, function($a, $b) {
                return strcmp($a['nom'], $b['nom']); // Trie par ordre alphabétique sur le nom
            });
        } elseif ($filtre === 'prix') {
            usort($newsList, function($a, $b) {
                return $a['price'] <=> $b['price']; // Trie par prix croissant
            });
        } elseif ($filtre === 'progression_1m') {
            usort($newsList, function($a, $b) {
                return $a['progression_1m'] <=> $b['progression_1m']; // Trie par progression 1 mois
            });
        } elseif ($filtre === 'progression_an') {
            usort($newsList, function($a, $b) {
                return $a['progression_an'] <=> $b['progression_an']; // Trie par progression 1 an
            });
        }

        // Si aucun filtre n'est sélectionné, ou si "rien" est sélectionné, affiche toutes les actions sans modification

    ?>
    <header class="header_accueil">
        <div class="enssemble">
            <div class="sniper"></div>
            <h1 >Connecté: <?php echo $pseudo; ?></h1>
            <h1>bourse</h1>
            <nav class="navdeco">
                <ul>
                    <li class="lideco"><a href="./new.php" style="color: white;">new</a></li>
                    <li class="lidecomenu1"><a href="./questionaire.html" style="color: white;">Portefeuille</a></li>
                    <li class="lidecomenu1"><a href="./questionaire.html" style="color: white;">Recherche</a></li>
                </ul>
            </nav>
        </div>  
    </header>
    <form method="GET" action="">
        <div class="filtre">
        <h1 style="text-align: center ; ">Filtrer par : </h1><br>
            <div class="radio-input">
                <input value="nom" name="filtre" id="value-1" type="radio">
                <label for="value-1">nom</label>

                <input value="prix" name="filtre" id="value-2" type="radio">
                <label for="value-2">prix</label>

                <input value="progression 1 mois" name="filtre" id="value-3" type="radio">
                <label for="value-3">progression 1 mois</label>

                <input value="progression 1 an" name="filtre" id="value-4" type="radio">
                <label for="value-4">progression 1 an</label>

                <input value="rien" name="filtre" id="value-5" type="radio">
                <label for="value-5">rien</label>
            </div>
        </div>
        <div class="center"><button type="submit" class="valider_filtre">Valider</button></div>
        
    </div>
    </form>
    <div class="flex_action">
        <?php
            foreach($newsList as $action): ?>
                <div class="card">
                    <a href="info.php?id=<?php echo $action['id']; ?>" ><!--- nathannnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnn--->
                        <button type="button" class="info-button">Info</button>
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
    </div>

    <footer>
    
    </footer>
</body>
</html>
