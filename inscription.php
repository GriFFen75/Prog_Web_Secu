<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>Prog_Web_Secu</title>
</head>

<body>
    <h1>Incription</h1>
    <button><a href="index.html">retour à la page index</a></button>
    <br><br>
    <form action="#" method="POST">
        <input type="text" name="username" id="username" placeholder="username" minlength="5" maxlength="512" required><br>
        <input type="password" name="password" id="password" placeholder="password" minlength="4" maxlength="512" required><br>
        <input type="submit" name="submit" id="submit" value="inscription">
    </form>

</body>
<br><br><br>
<?php
global $mysqli;
include("functions.php");
join_database();


//$result = $mysqli->query("SELECT * FROM `user`");
//$users = $result->fetch_assoc();
//print_r ($users);
//echo "<br>";


if (isset($_POST['username'])){
    if ($_POST['username'] != "") {
        if (isset($_POST["password"])) {
            if ($_POST["password"] != "") {
                if (isset($_POST["submit"])) {
                    $password_str = mysqli_real_escape_string($mysqli, $_POST['password']);
                    if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{4,512}$/', $password_str)){
                        $username_html = htmlspecialchars($_POST["username"]);
                        $username_str = mysqli_real_escape_string($mysqli, $_POST['username']);
                        $doublon = $mysqli->query("SELECT * FROM user WHERE username = '{$username_str}'")->fetch_assoc();
                        if ($doublon){
                            echo "Ce nom d'utilisateur est deja dans la base de donnée";
                        }
                        else{
                            insert_field_secure($username_str, password_hash($password_str, PASSWORD_BCRYPT, ['cost' => 10, 'salt' => "nEXEsAKkolF/o2F25o0SOjmOK1AxjsvPuYGExuWrpGmC0IKW3PDU26PodgFxcBM3"] ));
                            ?>
                            <h1>Bonjour <?php echo $username_html; ?></h1>
                            <?php
                        }
                    }
                    else{
                        print_r("Non non non");
                    }
                }
                else{
                    echo "Le mot de passe n'est pas assez fort";
                }
            }
        }
    }
}
?>