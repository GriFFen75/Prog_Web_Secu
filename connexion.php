<?php
if (isset($container->isLoggedIn) && $container->isLoggedIn) {
    // Redirige vers la page authentifiée
    header('Location: bonjour.php');
    exit;
}
    //require
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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
</head>

<body>
<h1>Connexion</h1>
<button><a href="index.html">retour à la page index</a></button>
<br><br>
<form action="traitement_connexion.php" method="POST" id="formulaire_connexion">
    <input type="text" name="username" id="username" placeholder="username" minlength="5" maxlength="512" required><br>
    <input type="password" name="password" id="password" placeholder="password" minlength="4" maxlength="512" required><br>
    <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $csrfToken; ?>">
    <input type="submit" name="submit" value="connexion">
    <!-- <div class="g-recaptcha" data-sitekey=<?php // echo $dbinfo["site_key"]; ?>></div> -->

</form>

</body>
