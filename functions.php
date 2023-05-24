<?php

$mysqli = NULL;

function join_database() {
    global $mysqli;

    $dbinfo = file_get_contents("join_db.json");
    $dbinfo = json_decode($dbinfo, true);

    $mysqli = mysqli_connect($dbinfo["domain"], $dbinfo["login"], $dbinfo["password"],$dbinfo["database"]);
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $mysqli=mysqli_init();
    if (!$mysqli)
    {
        die("mysqli_init failed");
    }

    if (!mysqli_real_connect($mysqli, $dbinfo["domain"], $dbinfo["login"], $dbinfo["password"], $dbinfo["database"]))
    {
        die("Connect Error: " . mysqli_connect_error());
    }
    return $mysqli;
}

function join_database_secure(){
    global $mysqli;

    ini_set ('error_reporting', E_ALL);
    ini_set ('display_errors', '1');
    error_reporting (E_ALL|E_STRICT);

    $dbinfo = file_get_contents("join_db.json");
    $dbinfo = json_decode($dbinfo, true);

    $mysqli = mysqli_init();
    mysqli_options ($mysqli, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

    $mysqli->ssl_set(NULL, NULL, 'key/Griffen.crt', NULL, NULL);
    $link = mysqli_real_connect ($mysqli, $dbinfo["domain"], $dbinfo["login"], $dbinfo["password"],$dbinfo["database"], 3306, NULL, MYSQLI_CLIENT_SSL);
    if (!$link)
    {
        die ('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");
    }
    return $mysqli;
}

function join_database_secure_PDO(){

    $dbinfo = file_get_contents("join_db.json");
    $dbinfo = json_decode($dbinfo, true);

    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/var/www/html/Prog_Web_Secu/key/Griffen.crt',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true
    ];

    $dsn = 'mysql:host=pws.local;port=3306;dbname=prog_web_secu';
    $usernameDB = $dbinfo["login"];
    $passwordDB = $dbinfo["password"];

    try {
        $bdd = new PDO($dsn, $usernameDB, $passwordDB, $options);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connexion réussie en utilisant SSL.";
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
}


function generateCSRFToken() {
    $token = bin2hex(random_bytes(32)); // Génère une chaîne aléatoire de 32 octets (256 bits)
    #$_SESSION['csrf_token'] = $token; // Stocke le jeton CSRF dans la session
    return $token;
}

// Vérification du jeton CSRF
function validateCSRFToken($token) {
    if (isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token) {
        return true;
    } else {
        return false;
    }
}

function insert_fields($table, $fields) {
    global $mysqli;
    $tab = array_keys($fields); //ici fields doit etre égale à un tableau ( dans notre cas : le message , l'id et quand le message à été posté)
    $keys = implode(",",$tab);
    $value = implode("','",$fields);

    if(preg_match('/[a-zA-Z_0-9,@!.?] */',$value) == 0){
        print " Mauvaise orthographe ";
        return null;
    }
    print_r("insertion dans la base de donnée");
    $mysqli->query("INSERT INTO $table ($keys) VALUES ('$value')") or die($mysqli->error);

    return 0;
}

function insert_field_secure($username, $password){
    global $mysqli;

    $sql = "INSERT INTO user (username, password) VALUES (?, ?)";
    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("ss", $username, $password);
// Exécution de la requête
    if ($stmt->execute()) {
        echo "Enregistrement inséré avec succès dans la base de données.";
    } else {
        echo "Erreur lors de l'insertion dans la base de données : " . $stmt->error;
    }
}


function logout(){
    global $container;
    global $sessionManager;

    $container->offsetUnset('username');
    $container->offsetUnset('isLoggedIn');
    $sessionManager->destroy();
    echo "<script>document.location.href='index.html';</script>";
}