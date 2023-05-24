<?php
ob_start(); //pour ne pas afficher le contenue du fichier
require "join_db.json";
$data = ob_get_clean();
require "vendor/autoload.php";
require "functions.php";

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

$query = "SELECT roles FROM user WHERE username = ?";

// Liez le paramètre à la requête
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $container->username); // "s" indique que le paramètre est une chaîne de caractères

$stmt->execute();
$result = $stmt->get_result();
$role = $result->fetch_assoc();

if (!isset($container->isLoggedIn) || !$container->isLoggedIn || $role["roles"] != "administrator") {
    header("Location: index.php");
}
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
<h1>Bonjour <?php echo $container->username ?></h1>
<br><br>
<button><a href="index.php">retour à la page index</a></button>
<br>
<button><a href="https://www.youtube.com/watch?v=xvFZjo5PgG0">Direction l'easter Eggs administrateur</a></button>
<form action="" method="post">
    <input type="submit" name="submit" value="logout">
</form>
</body>
</html>