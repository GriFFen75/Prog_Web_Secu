<?php

$mysqli = NULL;

function join_database() {
    #global $mysqli;

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
    ini_set ('error_reporting', E_ALL);
    ini_set ('display_errors', '1');
    error_reporting (E_ALL|E_STRICT);

    $dbinfo = file_get_contents("join_db.json");
    $dbinfo = json_decode($dbinfo, true);

    $db = mysqli_init();
    mysqli_options ($db, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

    $db->ssl_set('key/client-key.pem', 'key/client-cert.pem', 'key/Griffen.crt', NULL, NULL);
    $link = mysqli_real_connect ($db, $dbinfo["domain"], $dbinfo["login"], $dbinfo["password"],$dbinfo["database"], 3306, NULL, MYSQLI_CLIENT_SSL);
    if (!$link)
    {
        die ('Connect error (' . mysqli_connect_errno() . '): ' . mysqli_connect_error() . "\n");
    }
     return $db;
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


?>