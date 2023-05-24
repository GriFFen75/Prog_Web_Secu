<?php
//require
ob_start(); //pour ne pas afficher le contenue du fichier
require "join_db.json";
$data = ob_get_clean();
require "vendor/autoload.php";
require "functions.php";

//récupération des données dans le fichier json
$dbinfo = json_decode(file_get_contents("join_db.json"), true);

//utilisation des script
use Laminas\Session\SessionManager;
use Laminas\Session\Container;

//création de la session
$sessionManager = new SessionManager();
$sessionManager->start();
$container = new Container('connexion_session', $sessionManager);

$csrftoken = isset($container->csrftoken) ? $container->csrftoken : null; // Retrieve the data from the container


global $mysqli;
join_database_secure();

if (isset($container->isLoggedIn) && $container->isLoggedIn) {
    // Redirige vers la page authentifiée
    header('Location: bonjour.php');
    exit;
}

if (isset($_POST['username'])){
    if (is_string($_POST["username"]) && $_POST['username'] != "") {
        if (isset($_POST["password"])) {
            if (is_string($_POST["password"]) && $_POST["password"] != "") {
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
                        if ($_POST["csrf_token"] === $csrftoken && $_SERVER["HTTPS"] === "on" && $_SERVER['HTTP_HOST'] === "pws.local" && $_SERVER["REQUEST_URI"] === "/traitement_inscription.php"){

                            $password_str = mysqli_real_escape_string($mysqli, $_POST['password']);
                            if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{4,512}$/', $password_str)){
                                $username_html = htmlspecialchars($_POST["username"]);
                                $username_str = mysqli_real_escape_string($mysqli, $_POST['username']);

                                $query = "SELECT * FROM user WHERE username = ?";
                                $stmt = $mysqli->prepare($query);
                                $stmt->bind_param("s", $username_str);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $doublon = $result->fetch_assoc();

                                #$doublon = $mysqli->query("SELECT * FROM user WHERE username = '{$username_str}'")->fetch_assoc();
                                if ($doublon){
                                    echo "Ce nom d'utilisateur est deja dans la base de donnée";
                                }
                                else{
                                    insert_user_secure($username_str, password_hash($dbinfo["prefix"].$password_str.$dbinfo["sufix"], PASSWORD_BCRYPT));
                                    $container->username = $username_str;
                                    #echo "container username : ".$container->username;
                                    $container->isLoggedIn = true;

                                    header("Location: bonjour.php");
                                    exit();
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