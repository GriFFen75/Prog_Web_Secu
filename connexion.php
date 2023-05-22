<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
</head>

<?php
ob_start(); //pour ne pas afficher le contenue du fichier
require ("join_db.json");
$data = ob_get_clean();

require ("functions.php");

$dbinfo = file_get_contents("join_db.json");
$dbinfo = json_decode($dbinfo, true);

$csrfToken = generateCSRFToken();

?>

<body>
<h1>Connexion</h1>
<button><a href="index.html">retour à la page index</a></button>
<br><br>
<form action="bonjour.php#" method="POST" id="formulaire_connexion">
    <input type="text" name="username" id="username" placeholder="username" minlength="5" maxlength="512" required><br>
    <input type="password" name="password" id="password" placeholder="password" minlength="4" maxlength="512" required><br>
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    <input type="submit" name="submit" value="connexion">
    <!-- <div class="g-recaptcha" data-sitekey=<?php // echo $dbinfo["site_key"]; ?>></div> -->

</form>

</body>