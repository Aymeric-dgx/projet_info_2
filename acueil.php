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
        </div><br>
  
    <div class="container_accueil">
        
        <!-- Partie gauche -->
        <div class="middle">
                    <!-- From Uiverse.io by david-mohseni --> 
                    <div class="coin">
  <div class="side heads">
    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="100%" height="100%" version="1.1" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 4091.27 4091.73" xmlns:xlink="http://www.w3.org/1999/xlink">
 <g id="Layer_x0020_1">
  <metadata id="CorelCorpID_0Corel-Layer"></metadata>
  <g id="_1421344023328">
   <path fill="#F7931A" fill-rule="nonzero" d="M4030.06 2540.77c-273.24,1096.01 -1383.32,1763.02 -2479.46,1489.71 -1095.68,-273.24 -1762.69,-1383.39 -1489.33,-2479.31 273.12,-1096.13 1383.2,-1763.19 2479,-1489.95 1096.06,273.24 1763.03,1383.51 1489.76,2479.57l0.02 -0.02z"></path>
   <path fill="white" fill-rule="nonzero" d="M2947.77 1754.38c40.72,-272.26 -166.56,-418.61 -450,-516.24l91.95 -368.8 -224.5 -55.94 -89.51 359.09c-59.02,-14.72 -119.63,-28.59 -179.87,-42.34l90.16 -361.46 -224.36 -55.94 -92 368.68c-48.84,-11.12 -96.81,-22.11 -143.35,-33.69l0.26 -1.16 -309.59 -77.31 -59.72 239.78c0,0 166.56,38.18 163.05,40.53 90.91,22.69 107.35,82.87 104.62,130.57l-104.74 420.15c6.26,1.59 14.38,3.89 23.34,7.49 -7.49,-1.86 -15.46,-3.89 -23.73,-5.87l-146.81 588.57c-11.11,27.62 -39.31,69.07 -102.87,53.33 2.25,3.26 -163.17,-40.72 -163.17,-40.72l-111.46 256.98 292.15 72.83c54.35,13.63 107.61,27.89 160.06,41.3l-92.9 373.03 224.24 55.94 92 -369.07c61.26,16.63 120.71,31.97 178.91,46.43l-91.69 367.33 224.51 55.94 92.89 -372.33c382.82,72.45 670.67,43.24 791.83,-303.02 97.63,-278.78 -4.86,-439.58 -206.26,-544.44 146.69,-33.83 257.18,-130.31 286.64,-329.61l-0.07 -0.05zm-512.93 719.26c-69.38,278.78 -538.76,128.08 -690.94,90.29l123.28 -494.2c152.17,37.99 640.17,113.17 567.67,403.91zm69.43 -723.3c-63.29,253.58 -453.96,124.75 -580.69,93.16l111.77 -448.21c126.73,31.59 534.85,90.55 468.94,355.05l-0.02 0z"></path>
  </g>
 </g>
    </svg>
  </div>
  <div class="side tails">
    <svg xmlns="http://www.w3.org/2000/svg" class="svg_back" xml:space="preserve" width="100%" height="100%" version="1.1" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 4091.27 4091.73" xmlns:xlink="http://www.w3.org/1999/xlink">
 <g id="Layer_x0020_1">
  <metadata id="CorelCorpID_0Corel-Layer"></metadata>
  <g id="_1421344023328">
   <path fill="#F7931A" fill-rule="nonzero" d="M4030.06 2540.77c-273.24,1096.01 -1383.32,1763.02 -2479.46,1489.71 -1095.68,-273.24 -1762.69,-1383.39 -1489.33,-2479.31 273.12,-1096.13 1383.2,-1763.19 2479,-1489.95 1096.06,273.24 1763.03,1383.51 1489.76,2479.57l0.02 -0.02z"></path>
   <path fill="white" fill-rule="nonzero" d="M2947.77 1754.38c40.72,-272.26 -166.56,-418.61 -450,-516.24l91.95 -368.8 -224.5 -55.94 -89.51 359.09c-59.02,-14.72 -119.63,-28.59 -179.87,-42.34l90.16 -361.46 -224.36 -55.94 -92 368.68c-48.84,-11.12 -96.81,-22.11 -143.35,-33.69l0.26 -1.16 -309.59 -77.31 -59.72 239.78c0,0 166.56,38.18 163.05,40.53 90.91,22.69 107.35,82.87 104.62,130.57l-104.74 420.15c6.26,1.59 14.38,3.89 23.34,7.49 -7.49,-1.86 -15.46,-3.89 -23.73,-5.87l-146.81 588.57c-11.11,27.62 -39.31,69.07 -102.87,53.33 2.25,3.26 -163.17,-40.72 -163.17,-40.72l-111.46 256.98 292.15 72.83c54.35,13.63 107.61,27.89 160.06,41.3l-92.9 373.03 224.24 55.94 92 -369.07c61.26,16.63 120.71,31.97 178.91,46.43l-91.69 367.33 224.51 55.94 92.89 -372.33c382.82,72.45 670.67,43.24 791.83,-303.02 97.63,-278.78 -4.86,-439.58 -206.26,-544.44 146.69,-33.83 257.18,-130.31 286.64,-329.61l-0.07 -0.05zm-512.93 719.26c-69.38,278.78 -538.76,128.08 -690.94,90.29l123.28 -494.2c152.17,37.99 640.17,113.17 567.67,403.91zm69.43 -723.3c-63.29,253.58 -453.96,124.75 -580.69,93.16l111.77 -448.21c126.73,31.59 534.85,90.55 468.94,355.05l-0.02 0z"></path>
  </g>
 </g>
</svg></div>
</div>
            
            <div class="text-container">
                <h2>Dans notre jeu, le temps ne s'écoule pas en temps réel, mais en pseudo-temps. Cela signifie que le déroulement du jeu est accéléré par rapport à la réalité.</h2>

                <h2> 🕒 1 mois dans le jeu correspond à seulement 2 minutes dans la vraie vie.</h2>

                <h2>Grâce à ce système, vous pouvez progresser plus rapidement et voir les effets de vos actions en peu de temps. </h2>

                <h2>Que vous développiez votre stratégie ou que vous preniez des décisions importantes, chaque instant compte !</h2>

                <h2>>Profitez de cette mécanique pour optimiser votre gestion et atteindre vos objectifs plus vite. 🚀</h2>
            </div>
            
        </div>
    </div>
    <br>
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
</body>
</html>
