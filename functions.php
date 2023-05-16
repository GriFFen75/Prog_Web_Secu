<?php

$mysqli = NULL;

function join_database() {
    global $mysqli;

    $dbinfo = file_get_contents("join_db.json");
    $dbinfo = json_decode($dbinfo, true);
    $mysqli = new mysqli($dbinfo["domain"], $dbinfo["login"], $dbinfo["password"],$dbinfo["database"]);

    if ($mysqli->connect_error) {
        die("<br>Connection failed: " . $mysqli->connect_error);
    }
    return $mysqli;
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

?>