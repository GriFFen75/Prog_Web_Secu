<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
    <script nonce="captcha" src="https://www.google.com/recaptcha/api.js" async defer></script>
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
    <h1>Incription</h1>
    <button><a href="index.html">retour à la page index</a></button>
    <br><br>

    <form action="#" method="POST" id="formulaire_inscription">
        <input type="text" name="username" id="username" placeholder="username" minlength="5" maxlength="512" required><br>
        <input type="password" name="password" id="password" placeholder="password" minlength="4" maxlength="512" required><br>
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        <input type="submit" name="submit" id="submit" value="inscription">
        <div class="g-recaptcha" data-sitekey=<?php echo $dbinfo["site_key"]; ?>></div>

    </form>

</body>
<br><br><br>
<?php
global $mysqli;
join_database();
#join_database_secure();
#join_database_secure_PDO();

//$result = $mysqli->query("SELECT * FROM `user`");
//$users = $result->fetch_assoc();
//print_r ($users);
//echo "<br>";


if (isset($_POST['username'])){
    if ($_POST['username'] != "") {
        if (isset($_POST["password"])) {
            if ($_POST["password"] != "") {
                if (isset($_POST["submit"])) {


                    $recaptcha_response = $_POST['g-recaptcha-response'];
                    $secret_key = $dbinfo["secret_key"];

                    $url = 'https://www.google.com/recaptcha/api/siteverify';
                    $data = array(
                        'secret' => $secret_key,
                        'response' => $recaptcha_response
                    );

                    $options = array(
                        'http' => array(
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($data)
                        )
                    );

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    $response = json_decode($result, true);


                    if ($response['success']) {
                        #header('X-CSRF-Token : ' . $csrfToken);      && validateCSRFToken($_SERVER['HTTP_X_CSRF_Token'])
                        if (validateCSRFToken($csrfToken) && $_SERVER["HTTPS"] === "on" && $_SERVER['HTTP_HOST'] === "pws.local" && $_SERVER["REQUEST_URI"] === "/inscription.php"){

                            $password_str = mysqli_real_escape_string($mysqli, $_POST['password']);
                            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{4,512}$/', $password_str)){
                                $username_html = htmlspecialchars($_POST["username"]);
                                $username_str = mysqli_real_escape_string($mysqli, $_POST['username']);
                                $doublon = $mysqli->query("SELECT * FROM user WHERE username = '{$username_str}'")->fetch_assoc();
                                if ($doublon){
                                    echo "Ce nom d'utilisateur est deja dans la base de donnée";
                                }
                                else{
                                    insert_field_secure($username_str, password_hash($password_str, PASSWORD_BCRYPT, ['cost' => 10, 'salt' => "lameilleurdefensecestlattaaque"] ));
                                     ?>

                                    <h1>Bonjour <?php echo $username_html; ?></h1>

                                    <?php
                                }
                            }
                            else{
                                echo "Le mot de passe n'est pas assez fort";
                            }
                        }
                        else{
                            echo "pas de jeton CSRF et verif du lien faux";
                        }
                    }
                    else{
                        print_r("probleme avec le katchan");
                    }
                }
            }
        }
    }
}
?>