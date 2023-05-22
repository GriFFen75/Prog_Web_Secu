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
<h1>Bonjour tout le monde</h1>
<button><a href="index.html">retour à la page index</a></button>
<br><br>

</body>
</html>