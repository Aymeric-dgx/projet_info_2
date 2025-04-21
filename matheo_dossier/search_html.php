<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="search_css.css">
    <script src="scripts/script_search.js" defer></script>
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
                    <li class="lideco"><a href="./accueil.php" style="color: white;">Acuueil</a></li>
                    <li class="lidecomenu1"><a href="./profil_html.php" style="color: white;">Portefeuille</a></li>
                    <li class="lidecomenu1"><a href="./new.php" style="color: white;">news</a></li>
                    <li class="lidecomenu1" style="background-color:red;"><a href="logout.php" style="color: white;" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">Se déconnecter</a></li>
                </ul>
            </nav>
        </div>   
    </header>

    <main>
        <div class="top_box">
            <div class="search_bar">
                <div class="search_bar_logo"></div>
                <form id='search_form'>
                    <input class="search_bar_input" type="text" id="search_input" name="search" placeholder="Rechercher" required>
                </form>
            </div>
            <button id="best_players_button" class="sorting_button">Afficher les meilleurs joueurs</button>
        </div>

        <div id="result" class="searh_result">
        </div>
    </main>
</body>
</html>