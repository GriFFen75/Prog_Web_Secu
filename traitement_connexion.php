<?php
//require
ob_start(); //pour ne pas afficher le contenue du fichier
require "join_db.json";
$data = ob_get_clean();
require "vendor/autoload.php";
require "functions.php";

//récupération des données dans le fichier json
$dbinfo = file_get_contents("join_db.json");
$dbinfo = json_decode($dbinfo, true);

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

try {
    if (isset($_POST['username'])) {
        if (is_string($_POST["username"]) && $_POST['username'] != "") {
            if (isset($_POST["password"])) {
                if (is_string($_POST["password"]) && $_POST["password"] != "") {
                    if (isset($_POST["submit"])) {
                        if (isset($_POST['g-recaptcha-response'])) {
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
                                #print_r("container : ".$csrftoken);
                                #echo "<br>";
                                #print_r("\$_POST : ".$_POST["csrf_token"]);
                                if (isset($csrftoken) && isset($_POST["csrf_token"])) {
                                    if ($_POST["csrf_token"] === $csrftoken && $_SERVER["HTTPS"] === "on" && $_SERVER['HTTP_HOST'] === "pws.local" && $_SERVER["REQUEST_URI"] === "/traitement_connexion.php") {
                                        #print_r($mysqli);
                                        $password_str = mysqli_real_escape_string($mysqli, $_POST['password']);

                                        $username_html = htmlspecialchars($_POST["username"]);
                                        $username_str = mysqli_real_escape_string($mysqli, $_POST['username']);

                                        $stmt = $mysqli->prepare("SELECT * FROM user WHERE username = ?");
                                        $stmt->bind_param("s", $username_str);
                                        $stmt->execute();
                                        $result = $stmt->get_result();
                                        $present = $result->fetch_assoc();

                                        if ($present) {
                                            #print_r($present);
                                            if (!password_verify($dbinfo["prefix"] . $password_str . $dbinfo["sufix"], $present["password"])) {
                                                #echo "c'est pas le bon mot de passe";
                                            } else {
                                                $container->username = $username_str;
                                                $container->isLoggedIn = true;

                                                #echo "<script>document.location.href='bonjour.php';</script>";
                                                header("Location: bonjour.php");
                                                exit();
                                            }
                                        } else {
                                            #echo "<script>document.location.href='connexion.php';</script>";
                                            header("Location: connexion.php");
                                            exit();
                                            #echo "non non non";
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
catch (mysqli_sql_exception){
    header("Location: index.php");
    exit();
}

?>