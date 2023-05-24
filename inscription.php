<?php

//require
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

if (isset($container->isLoggedIn) && $container->isLoggedIn) {
    // Redirige vers la page authentifiée
    header('Location: bonjour.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
    <script nonce="captcha" src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <h1>Incription</h1>
    <button><a href="index.html">retour à la page index</a></button>
    <br><br>

    <form action="traitement_inscription.php" method="POST" id="formulaire_inscription">
        <input type="text" name="username" id="username" placeholder="username" minlength="5" maxlength="512" required><br>
        <input type="password" name="password" id="password" placeholder="password" minlength="15" maxlength="512" required><br>
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        <input type="submit" name="submit" id="submit" value="inscription">
        <div class="g-recaptcha" data-sitekey=<?php echo $dbinfo["site_key"]; ?>></div>

    </form>

</body>
<br><br><br>
