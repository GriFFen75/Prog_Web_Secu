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
        <input type="text" name="username" id="username" placeholder="username"><br>
        <input type="password" name="password" id="password" placeholder="password"><br>
        <input type="submit" name="submit" id="submit" value="inscription">
    </form>

</body>
<br><br><br>
<?php
global $mysqli;
include ("functions.php");
join_database();


//$result = $mysqli->query("SELECT * FROM `user`");
//$users = $result->fetch_assoc();
//print_r ($users);
//echo "<br>";


//faire du regex pour check si le mdp

if (isset($_POST['username'])){
    if ($_POST['username'] != "") {
        if (isset($_POST["password"])) {
            if ($_POST["password"] != "") {
                if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).{8,512}$/', $_POST["password"])){
                    if (isset($_POST["submit"])) {
                        $username_str = mysqli_real_escape_string($mysqli, $_POST['username']);
                        $doublon = $mysqli->query("SELECT * FROM user WHERE username = '{$username_str}'")->fetch_assoc();
                        if ($doublon){
                            echo "Ce nom d'utilisateur est deja dans la base de donnée";
                        }
                        else{
                            insert_fields('user', ["username" => $username_str, "password" => password_hash(mysqli_real_escape_string($mysqli, $_POST['password']), PASSWORD_BCRYPT)]);
                            ?>
                            <h1>Bonjour <?php echo $_POST["username"]; ?></h1>
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

