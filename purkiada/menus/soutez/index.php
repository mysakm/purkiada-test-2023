<?php
require("../../data/sql.php");
session_start();
if(!empty($_POST["login"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT * FROM `zaci` WHERE username = "' . $_POST["login"] . '" AND pwd = "' . $_POST["password"] . '"';
    $result = $connect->query($query) or die("Fault1");
    $connect->close();
    $resultUser = ($result->fetch_object())->zak_id;
    if (!empty($resultUser)) {
        header("Location: ./soutez-otazky");
        $_SESSION["username"] = $_POST["login"];
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
        <input type="text" name="login">
        <input type="password" name="password">
        <input type="submit">
        <?php
            if (isset($_POST["login"])) {
                echo("Wrong login details");
            }
        ?>
    </form>
</body>
</html>