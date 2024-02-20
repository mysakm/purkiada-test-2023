<?php
session_start();
require("../../data/sql.php");
if(!empty($_SESSION["access-key"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT * FROM `session_management` WHERE `session_id` = "' . $_SESSION["access-key"] . '"';
    $result = $connect->query($query) or die("Fault");
    $connect->close();
    $resultUser = ($result->fetch_object())->user_id;
    if(empty($resultUser)){
        echo("<!DOCTYPE html><head><link href='./adminStyle.css' rel='stylesheet'></head><body style='background-color:black'><p class='scary'>YOU SHOULDN'T BE, HERE.</p></body></html>");
        header('HTTP/1.0 403 Forbidden', true, 403);
        header("Location: ../");
        die("Page access forbidden 403.");
    }else{
        menus();
    }
}else{
    echo("<!DOCTYPE html><head><link href='./adminStyle.css' rel='stylesheet'></head><body style='background-color:black'><p class='scary'>YOU SHOULDN'T BE, HERE.</p></body></html>");
    header('HTTP/1.0 403 Forbidden', true, 403);
    header("Location: ../");
    die("Page access forbidden 403.");
}
function menus(){
    require("../../data/sql.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purkiáda - upravit email</title>
</head>
<?php
    if(isset($_POST["email-text"])){
        $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $submitEmailText = str_replace("\r\n", "<br>", $_POST['email-text']);
        $query = 'UPDATE `competition_status` SET `email_template`= "' . $submitEmailText . '" WHERE 1';
        $result = $connect->query($query) or die("Fault1");
        $connect->close();
    }
?>
<body>
    <a href="./index.php"><p style="background-color:lightgray; width:30px">Zpět</p></a>
    <form action="" id="emailForm" method="POST">
        <textarea form="emailForm" name="email-text" rows="20" cols="75"><?php
        $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $query = "SELECT email_template FROM `competition_status`";
        $result = $connect->query($query) or die("Fault1");
        $connect->close();
        while($row = $result->fetch_object()) {
            $emailText = $row->email_template;
        }
        $emailText = str_replace("<br>", "\n", $emailText);
        echo($emailText);
        ?></textarea><br>
        <input type="submit" value="Nastavit">
    </form>
    <p>Speciální znaky:</p>
    <p>{currentYear} - doplní rok podle aktuálního času</p>
    <p>{eventDate} - doplní datum podle dne soutěže (nastavitelné v hlavním admin menu, formát d. m. y)</p>
    <p>{eventYear} - doplní rok podle dne soutěže</p>
    <p>{eventDay} - doplní jméno dne v týdnu podle dne soutěže</p>
</body>
</html>
<?php
}?>