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
    require("../../data/sql.php");
    echo("Celkem:<br>");
    $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT `zaci`.`name`, `zaci`.`surname`, `zaci`.`login`, SUM(points) AS soucet FROM `answers` INNER JOIN `zaci` ON `answers`.`zak_id` = `zaci`.`zak_id` WHERE 1 GROUP BY `answers`.`zak_id` ORDER BY `soucet` DESC';
    $result = $connect->query($query) or die("Fault");
    $connect->close();
    $competitors = array();
    while ($row = $result->fetch_object()) {
        $competitors[] = array(
            'name' => $row->name,
            'surname' => $row->surname,
            'login' => $row->login,
            'pointSum' => $row->soucet,
            'place' => ""
        );
    } // získání dat o bodech
    $placement = 1;
    $placementLastVar = 0;
    for ($x = 0; $x < count($competitors); $x++) {
        if (!isset($competitors[$x+1]) or $competitors[$x]['pointSum'] != $competitors[$x+1]['pointSum']){
            $competitors[$x]['place'] = $placement . ". místo";
            $placement++;
        }else{
            $placementLastVar = $placement;
            $xOffset = $x;
            while ($competitors[$xOffset+1] != null and $competitors[$xOffset]['pointSum'] == $competitors[$xOffset+1]['pointSum']) {
                $xOffset++;
                $placementLastVar++;
            }
            for ($y = $x; $y <= $xOffset; $y++){
                $competitors[$y]['place'] = $placement . ". - " . $placementLastVar . ". místo";
            }
            $x = $xOffset;
            $placement = $placementLastVar + 1;
        } // zpracování výsledné pozice
    }
    foreach ($competitors as $competitor) {
        echo("Jméno: " . $competitor['name'] . " " . $competitor['surname'] . " login: " . $competitor['login'] . ", celkem bodů: " . $competitor['pointSum'] . ", umístění: " . $competitor['place'] . "<br>");
    } // výstup na stránku

    echo("<br>Jednotlivé body:<br>");
    $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT `zaci`.`login`, `answers`.`question_number`, `answers`.`points` FROM `answers` INNER JOIN `zaci` ON `answers`.`zak_id` = `zaci`.`zak_id` WHERE 1';
    $result = $connect->query($query) or die("Fault");
    $connect->close();
    while ($row = $result->fetch_object()) {
        echo("login: " . $row->login . ", otázka číslo: " . $row->question_number . ", počet bodů: " . $row->points . "<br>");
    }
}
?>