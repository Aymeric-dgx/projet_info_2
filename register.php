<?php
    // Pour la BDD
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

    if(isset($_POST['register'])){
        $identifiant=$_POST['identifiant'];
        $pass=$_POST['pass'];

        $_request=$bdd->prepare("INSERT INTO utilisateur VALUES(0, :identifiant, :pass)");
        $_request->execute(
            array(
                "identifiant" => $identifiant,
                "pass" => $pass
            )
        );
        $reponse=$_request->fetchAll(PDO::FETCH_ASSOC);
        var_dump($reponse);
    }
?>