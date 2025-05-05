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
                <li class="lideco"><a href="./accueil.php" style="color: white;">Acuueil</a></li>
                <li class="lidecomenu1"><a href="./new.php" style="color: white;">news</a></li>
                <li class="lidecomenu1"><a href="./search_html.php" style="color: white;">Recherche</a></li>
                <li class="lidecomenu1" style="background-color:red;"><a href="logout.php" style="color: white;" onclick="return confirm('Voulez-vous vraiment vous déconnecter ?')">Se déconnecter</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="main_style">
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
        <div class="bottom_box">
            <div>
                <h3>Graphique n°1 : Evolution du solde sur les 12 derniers mois</h3>
                <h3>Graphique n°2 : Evolution de la valeur total (solde + actions) sur les 12 derniers mois</h3>
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
</main>
</body>
</html>