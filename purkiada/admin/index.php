<?php
session_start();
require('../data/sql.php');

if(!empty($_POST["username"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT `user_id` FROM `permanent_logins` WHERE `username` = "' . $_POST["username"] . '" AND `pwdHash` = "' . hash('ripemd160', $_POST['pwd']) . '"';
    $result = $connect->query($query) or die("Fault");
    $connect->close();
    $workWith = $result->fetch_object();
    if(!empty($workWith)){
        $resultUser = $workWith->user_id;
    }
    if(!empty($resultUser)){
        $sid = "";
        for ($x=0; $x < 40; $x++) { 
            $prepid = random_int(0, 15);
            $sid .= dechex($prepid);
        }
        $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $query = 'INSERT INTO `session_management`(`session_id`, `user_id`) VALUE ("' . $sid . '",' . $resultUser . ')';
        $result = $connect->query($query) or die("Fault");
        $connect->close();
        $_SESSION["access-key"] = $sid;
        header("Location: ./menu");
        die();
    }else{
        echo(hash('ripemd160', $_POST['pwd']));
        login("Wrong login info.");
    }
}elseif(!empty($_SESSION["access-key"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT * FROM `session_management` WHERE `session_id` = "' . $_SESSION["access-key"] . '"';
    echo($query);
    $result = $connect->query($query) or die("Fault");
    $connect->close();
    $workWith = $result->fetch_object();
    if(!empty($workWith)){
        $resultUser = $workWith->user_id;
    }
    if(!empty($resultUser)){
        header("Location: ./menu");
        die();
    }else{
        login("Your session has expired. Please log in again.");
    }
}else{
    login("");
}
function login($reason){
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Purkiáda - admin</title>
    </head>
    <body>
        <p style="color:red"><?php echo($reason);?></p>
        <form action="" method="POST">
            <label for="name">Uživatelské jméno: </label>
            <input type="text" id="name" name="username">
            <label for="pwd">Heslo: </label>
            <input type="password" id="pwd" name="pwd">
            <input type="submit" value="Přihlásit se">
        </form>
    </body>
    </html>
    <?php
}
?>