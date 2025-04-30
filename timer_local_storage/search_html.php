<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="search_css.css">
    <script src="scripts/script_search.js" defer></script>
    <script src="launch_update.js"></script>
    <?php session_start();
        $_SESSION['user_id'] = 4; // Pour test, à supprimer
    ?>
</head>
<body>
    <header class="header_accueil">
        <div class="enssemble">
            <div class="sniper"></div>
            <h1 >Connecté: <?php echo $_SESSION['pseudo']; ?></h1>
            <h1 class="logo">Trade</h1>
            <nav class="navdeco">
                <ul>
                    <li class="lideco"><a href="./acceuil.php" style="color: white;">Acceuil</a></li>
                    <li class="lidecomenu1"><a href="profil_html.php" style="color: white;">Profil</a></li>
                    <li class="lidecomenu1"><a href="./news.html" style="color: white;">News</a></li>
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