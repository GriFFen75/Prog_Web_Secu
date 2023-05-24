<?php
ob_start(); //pour ne pas afficher le contenue du fichier
require "join_db.json";
$data = ob_get_clean();
require 'vendor/autoload.php';
require "functions.php";

use Laminas\Session\SessionManager;
use Laminas\Session\Container;

$sessionManager = new SessionManager();
$sessionManager->start();
$container = new Container('connexion_session', $sessionManager);

if (!isset($container->isLoggedIn) || !$container->isLoggedIn) {
    // Redirige vers la page de connexion
    header("Location: index.php");
    exit();
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
<?php

$dbinfo = file_get_contents("join_db.json");
$dbinfo = json_decode($dbinfo, true);

$csrfToken = generateCSRFToken();

$username = $container->username;

?>
<body>
<h1>Est-ce que c'est bon pour vous ?</h1>
<button><a href="index.php">retour à la page index</a></button>
<br><br>
<h2>Bienvenue, <?php echo $username ?></h2>
<form action="" method="post">
    <input type="submit" name="submit" value="logout">
</form>

</body>
</html>