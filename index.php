<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <link rel="website icon" href="photo/login.png">
</head>

<body class="body_login">
    <?php
    // Pour la BDD
    session_start(); // ! Très important : à mettre en tout début du fichier
    $error_msg = "";

    $servername = "localhost";
    $username = "root";
    $password = "";

    try {
        $bdd = new PDO("mysql:host=$servername;dbname=bourse", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "ERREUR : " . $e->getMessage();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $identifiant = $_POST['identifiant'];
        $pass = $_POST['pass'];
    
        if (!empty($identifiant) && !empty($pass)) {
            // Préparation sécurisée de la requête
            $stmt = $bdd->prepare("SELECT * FROM utilisateur WHERE identifiant = :identifiant AND Password = :pass");
            $stmt->bindParam(':identifiant', $identifiant);
            $stmt->bindParam(':pass', $pass); // hasher le mot de passe (ex : avec password_verify)
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id']; // Ou autre identifiant unique
                $_SESSION['identifiant'] = $user['identifiant'];
    
                header("Location: new.php"); // Redirection après connexion
                exit();
            } else {
                $error_msg = "Identifiant ou mot de passe incorrect.";
            }
        } else {
            $error_msg = "Veuillez remplir tous les champs.";
        }
    }
    ?>

   
        <div class="bienvenue">
            <h1> Bienvenue sur notre simulateur de bourse</h1>
            <p>
                Vous rêvez d’investir en bourse, mais vous voulez d’abord tester vos stratégies sans risque ? Notre
                simulateur est l’outil parfait pour vous ! <br>
                <br>
                💡 Apprenez à trader en toute sécurité. <br>
                📊 Expérimentez des stratégies d’investissement sans perdre un centime.<br>
                📈 Suivez l’évolution des marchés en temps réel.<br>
                <br>
                Connectez-vous dès maintenant et prenez vos premières décisions en tant que trader ! 🚀<br>
                <br>
                Bonne simulation et bons investissements ! 💰<br>
            </p>
        </div>

        <div class="login" id="loginDiv">
            <h1 class="h1login">Connexion</h1>
            <form action="" method="Post" class="formlogin">

                <div class="case">
                    <input type="text" required class="inputlog" placeholder="identifiant" name="identifiant" id="identifiant" />

                    <input type="password" required class="inputlog" placeholder="mot de passe" name="pass" id="pass" />

                    <div class="flex">
                        <input type="submit" value="Valider" class="inputlogin" name="ok">
                        <button type="button" class="inputlogin" onclick="switchToRegister()">S'inscrire</button>
                    </div>

                    <p class="credit">Projet réalisé par Mathéo, Aymeric et Nathan ✨</p>
                </div>
            </form>

            <?php
            if (!empty($error_msg)) {
                echo "<p style='color:red;'>$error_msg</p>";
            }
            ?>

        </div>

        <!-- Div Inscription (masquée au début) -->
        <div class="login" id="registerDiv" style="display: none;">
            <h1 class="h1login">Inscription</h1>

            <form action="register.php" method="POST" class="formlogin">
                <div class="case">
                <input type="text" required class="inputlog" placeholder="identifiant" name="identifiant" id="identifiant" />
                <input type="password" required class="inputlog" placeholder="mot de passe" name="pass" id="pass" />
                 <!--<input type="password" required class="inputlog" placeholder="Confirmer le mot de passe" name="confirm_pass" /> -->

                <div class="flex">
                    <input type="submit" value="S'inscrire" class="inputlogin" name="register">
                    <button type="button" class="inputlogin" onclick="switchToLogin()">Retour</button>
                </div>
          
                <p class="credit">Projet réalisé par Mathéo, Aymeric et Nathan ✨</p>
                </div>
            </form>
        </div>

        <script>
        function switchToRegister() {
            document.getElementById("loginDiv").style.display = "none";
            document.getElementById("registerDiv").style.display = "block";
        }

        function switchToLogin() {
            document.getElementById("registerDiv").style.display = "none";
            document.getElementById("loginDiv").style.display = "block";
        }
    </script>
</body>

</html>