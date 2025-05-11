<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <link rel="website icon" href="photo/login.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .message {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            width: 50%;
            margin: auto;
        }
        .success { background-color: green; color: white; }
        .error { background-color: red; color: white; }
    </style>
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
    if (isset($_POST['register'])) {
        $pseudo = $_POST['pseudo'];
        $mdp = $_POST['mdp'];
        $email = $_POST['email'];

        // Vérifier si l'identifiant ou l'email existent déjà
        $check = $bdd->prepare("SELECT id FROM utilisateur WHERE pseudo = :pseudo OR email = :email");
        $check->execute(["pseudo" => $pseudo, "email" => $email]);

        if ($check->rowCount() > 0) {
            $_SESSION['message'] = "❌ pseudo ou email déjà utilisé.";
        } else {
            // Insertion dans la base de données
            $_request = $bdd->prepare("INSERT INTO utilisateur(pseudo, email, mdp) VALUES(:pseudo, :email, :mdp)");
            $success = $_request->execute(
                array(
                    "pseudo" => $pseudo,
                    "mdp" => $mdp,
                    "email" => $email
                )
            );
            $_SESSION['message'] = $success ? "✅ Inscription réussie !" : "❌ Erreur lors de l'inscription.";
        }
        header("Location: ".$_SERVER['PHP_SELF']); // Recharge la page pour afficher le message
        exit();
    }elseif (isset($_POST['login'])) {
        $pseudo = $_POST['pseudo'];
        $mdp = $_POST['mdp'];

        if (!empty($pseudo) && !empty($mdp)) {
            // Préparation sécurisée de la requête
            $stmt = $bdd->prepare("SELECT * FROM utilisateur WHERE pseudo = :pseudo AND mdp = :mdp");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->bindParam(':mdp', $mdp);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Connexion réussie
                $_SESSION['user_id'] = $user['id']; // Ou autre identifiant unique
                $_SESSION['pseudo'] = $user['pseudo'];
                $_SESSION['email'] = $user['email'];
                header("location:accueil.php");
            } else {
                $_SESSION['message'] ="❌ Information faux.";
            }
        } else {
            $error_msg = "Veuillez remplir tous les champs.";
        }
    }elseif (isset($_POST['change_password'])) {
        $pseudo = $_POST['pseudo'];
        $current_pass = $_POST['current_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_new_pass = $_POST['confirm_new_pass'];

        // Vérification si le mot de passe actuel est correct
        $stmt = $bdd->prepare("SELECT * FROM utilisateur WHERE pseudo = :pseudo AND mdp = :current_pass");
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':current_pass', $current_pass);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Vérifier si les nouveaux mots de passe correspondent
            if ($new_pass === $confirm_new_pass) {
                // Mettre à jour le mot de passe
                $update_stmt = $bdd->prepare("UPDATE utilisateur SET mdp = :new_pass WHERE pseudo = :pseudo");
                $update_stmt->bindParam(':new_pass', $new_pass);
                $update_stmt->bindParam(':pseudo', $pseudo);

                if ($update_stmt->execute()) {
                    $_SESSION['message'] = "✅ Mot de passe changé avec succès.";
                } else {
                    $_SESSION['message'] = "❌ Une erreur est survenue lors du changement du mot de passe.";
                }
            } else {
                $_SESSION['message'] = "❌ Les nouveaux mots de passe ne correspondent pas.";
            }
        } else {
            $_SESSION['message'] = "❌ Mot de passe actuel incorrect.";
        }

        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirige vers la page précédente
        exit();
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
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p style='color: ".(strpos($_SESSION['message'], '✅') !== false ? 'green' : 'red').";'>" . $_SESSION['message'] . "</p>";
        unset($_SESSION['message']); // Efface le message après l'affichage
    }
    ?>
    <form action="" method="Post" class="formlogin">
        <div class="case">
            <input type="text" required class="inputlog" placeholder="pseudo" name="pseudo" id="pseudo" value="" autocomplete="off" />

            <input type="password" required class="inputlog" placeholder="mot de passe" name="mdp" id="pass" value="" autocomplete="off" />

            <div class="flex">
                <input type="submit" value="Se connecter" class="inputlogin" name="login">
                &nbsp;
                <button type="button" class="inputlogin" onclick="switchToRegister()">S'inscrire</button>
                &nbsp;
                <button type="button" class="inputlogin" onclick="switchToPass()">Changer password</button>
            </div>

            <p class="credit">Projet réalisé par Mathéo, Aymeric et Nathan ✨</p>
        </div>
    </form>

</div>

<!-- Div Inscription (masquée au début) -->
<div class="login" id="registerDiv" style="display: none;">
    <h1 class="h1login">Inscription</h1>

    <form action="" method="POST" class="formlogin" onsubmit="return validateForm()">
        <div class="case">
            <input type="text" required class="inputlog" placeholder="pseudo" name="pseudo" id="pseudo" value="" />
            <input type="email" required class="inputlog" placeholder="email" name="email" id="email" value="" />
            <input type="password" required class="inputlog" placeholder="mot de passe" name="mdp" id="register_pass" value=""/>
            <input type="password" required class="inputlog" placeholder="Confirmer le mot de passe" name="confirm_pass" id="confirm_pass" value=""/>

            <div class="flex">
                <input type="submit" value="S'inscrire" class="inputlogin" name="register">
                <button type="button" class="inputlogin" onclick="switchToLogin()">Retour</button>
            </div>
            <p class="credit">Projet réalisé par Mathéo, Aymeric et Nathan ✨</p>
        </div>
    </form>
</div>

<div class="login" id="changePasswordDiv" style="display: none;">
    <h1 class="h1login">Changer mot de passe</h1>
    <form action="" method="Post" class="formlogin" onsubmit="return validatepass()">
        <div class="case">
            <input type="text" required class="inputlog" placeholder="pseudo" name="pseudo" id="pseudo" value="" />
            <input type="password" required class="inputlog" placeholder="Mot de passe actuel" name="current_pass" id="current_pass" value="" />
            <input type="password" required class="inputlog" placeholder="Nouveau mot de passe" name="new_pass" id="new_pass" value="" />
            <input type="password" required class="inputlog" placeholder="Confirmer le nouveau mot de passe" name="confirm_new_pass" id="confirm_new_pass" value="" />

            <div class="flex">
                <input type="submit" value="Changer le mot de passe" class="inputlogin" name="change_password">
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
        document.getElementById("changePasswordDiv").style.display = "none";
    }

    function switchToLogin() {
        document.getElementById("registerDiv").style.display = "none";
        document.getElementById("loginDiv").style.display = "block";
        document.getElementById("changePasswordDiv").style.display = "none";
    }
    function switchToPass(){
        document.getElementById("loginDiv").style.display = "none";
        document.getElementById("registerDiv").style.display = "none";
        document.getElementById("changePasswordDiv").style.display = "block";
    }
    // Fonction de validation du formulaire
    function validateForm() {
        var pass = document.getElementById("register_pass").value.trim();  // Utilisation de trim() pour enlever les espaces
        var confirmPass = document.getElementById("confirm_pass").value.trim();  // Utilisation de trim() pour enlever les espaces

        // Vérifie si les deux mots de passe sont identiques
        if (pass !== confirmPass) {
            alert("Les mots de passe ne correspondent pas.");
            return false; // Empêche l'envoi du formulaire si les mots de passe ne correspondent pas
        }
        return true; // Si les mots de passe correspondent, on soumet le formulaire
    }

    function validatepass() {
        var pass = document.getElementById("new_pass").value.trim();  // Utilisation de trim() pour enlever les espaces
        var confirmPass = document.getElementById("confirm_new_pass").value.trim();  // Utilisation de trim() pour enlever les espaces

        // Vérifie si les deux mots de passe sont identiques
        if (pass !== confirmPass) {
            alert("Les mots de passe ne correspondent pas.");
            return false; // Empêche l'envoi du formulaire si les mots de passe ne correspondent pas
        }
        return true; // Si les mots de passe correspondent, on soumet le formulaire
    }


</script>
</body>
</html>