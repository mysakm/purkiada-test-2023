<?php
session_start();
require("../../data/sql.php");
if(!empty($_SESSION["access-key"])){
    $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purkiáda - nastavení stránky výsledků</title>
</head>
<body>
    <?php
    if(isset($_POST["resultsEdit"])){
        $mainPageFile = fopen("../../menus/vysledky/index.php", "w") or die("Chyba otevření souboru.");
        fwrite($mainPageFile, $_POST["resultsEdit"]);
        fclose($mainPageFile);
    }
    ?>
    <form action="" id="resultsForm" type="POST">
        <textarea form="resultsForm" name="resultsEdit" rows="40" cols="150">
            <?php
            readfile("../../menus/vysledky/index.php");
            ?>
        </textarea>
        <p></p>
        <input type="submit" value="Odeslat">
    </form>
</body>
</html>
<?php
}
?>