<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <h1>Incription</h1>
    <button><a href="index.html">retour à la page index</a></button>
    <br><br>
    <form action="#" method="POST">
        <input type="text" name="username" id="username" placeholder="username" minlength="5" maxlength="512" required><br>
        <input type="password" name="password" id="password" placeholder="password" minlength="4" maxlength="512" required><br>
        <input type="submit" name="submit" id="submit" value="inscription">
        <div class="g-recaptcha" data-sitekey="6LeWZB0mAAAAAM6y6mE8q0sDFfpRkMISvwoHu-_m"></div>

    </form>

</body>
<br><br><br>
<?php
global $mysqli;
include("functions.php");
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
                    $secret_key = '6LeWZB0mAAAAAHZU1nQKTJD8jXe3qWUL9D8bbtSm';

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
                        $password_str = mysqli_real_escape_string($mysqli, $_POST['password']);
                        if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{4,512}$/', $password_str)){
                            $username_html = htmlspecialchars($_POST["username"]);
                            $username_str = mysqli_real_escape_string($mysqli, $_POST['username']);
                            $doublon = $mysqli->query("SELECT * FROM user WHERE username = '{$username_str}'")->fetch_assoc();
                            if ($doublon){
                                echo "Ce nom d'utilisateur est deja dans la base de donnée";
                            }
                            else{
                                insert_field_secure($username_str, password_hash($password_str, PASSWORD_BCRYPT, ['cost' => 10, 'salt' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDMKqUrabOE26b0oLF1gP1GGUCYv+H6GZnHEATOoRTMwFHmhTxVIhWd2p+YMkMNe49f/+kQRdniMY/mnEXEsAKkolF/o2F25o0SOjmOK1AxjsvPuYGExuWrpGmC0IKW3PDU26PodgFxcBM3qNB4iKrRyHc5ENYszlH7YMFeMXnpIWrczpvPRGOtitn8BNbHKwcKOUmZYoK7EzT61o96Cev395aW5AaGoijQO6qGIsXjzAfslC21CTa3DXc/EDg+o5b+wMRcSBTUovhWrwn9aluGWp5hx0lUUMuU9I7OiR9AhO6UYCIUnCgNTXQfK3gjU0EMQJdtYr9arNi4l9UrKupJAgMBAAECggEAJl4tRAdNMT6GFZrNPqPK9Q1x0kLdGL8O4xXjkWE8I25Q9d08BVOudEfNjjCD4VjDDOtuxRwbYiKmRFRB2ECrfnzyi+YMPuf8wtwmTM1e+LE5"] ));
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
                        print_r("probleme avec le katchan");
                    }
                }
            }
        }
    }
}
?>