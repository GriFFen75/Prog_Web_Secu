<?php
ob_start(); //pour ne pas afficher le contenue du fichier
require ("join_db.json");
$data = ob_get_clean();
require "vendor/autoload.php";
require "functions.php";

//importe du fichier de variable
$dbinfo = file_get_contents("join_db.json");
$dbinfo = json_decode($dbinfo, true);

//génération du token
$csrfToken = generateCSRFToken();

//utilisation des script
use Laminas\Session\Container;
use Laminas\Session\SessionManager;

//création de la sessions
$sessionManager = new SessionManager();
$sessionManager->start();
$container = new Container('connexion_session', $sessionManager);

//stockage du token
$container->csrftoken = $csrfToken;
#print_r($container->csrftoken);

global $mysqli;
join_database_secure();

if (isset($_POST["submit"])){
    logout();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
</head>
<body>
    <h1>Bonjour tout le monde</h1>
    <?php
    if (!isset($container->isLoggedIn) || !$container->isLoggedIn){
        ?>
        <button id="bouton_inscription"><a href="inscription.php">Direction le formulaire d'inscription</a></button>
        <br>
        <button id="bouton_connexion"><a href="connexion.php">Direction le formulaire de connexion</a></button>
        <br>
        <?php
    }
    ?>
    <button><a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Direction l'easter Eggs</a></button>
    <br>
    <?php

    if (isset($container->isLoggedIn) && $container->isLoggedIn) { ?>
        <button id="bouton_bonjour"><a href="bonjour.php">Direction la page bonjour</a></button>
        <br>
        <?php

        $query = "SELECT roles FROM user WHERE username = ?";

        // Liez le paramètre à la requête
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $container->username); // "s" indique que le paramètre est une chaîne de caractères

        $stmt->execute();
        $result = $stmt->get_result();
        $role = $result->fetch_assoc();

        if ($role["roles"] === "administrator"){
            ?>
            <button id="bouton_admin"><a href="admin.php">Direction la page d'administration</a></button>
            <?php
        }
        ?>
        <form action="" method="post">
            <input type="submit" name="submit" value="logout">
        </form>
    <?php
    }
    ?>

</body>
</html>