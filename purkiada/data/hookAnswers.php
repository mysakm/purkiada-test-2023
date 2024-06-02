<?php
require("./sql.php");
if(isset($_POST["query"]) and isset($_POST['points']) and isset($_POST['question_number']) and isset($_POST['zak_id'])){
    $connect = new mysqli($host, $user, $pass, $db) or die("Připojení se nezdařilo.");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = "SELECT `competition_open` FROM `competition_status` WHERE 1";
    $result = $connect->query($query) or die("Chyba získání odpovědí");
    $isCompRunning = ($result->fetch_object())->competition_open;
    $intPoints = (int)$_POST['points'];
    $intQNum = (int)$_POST['question_number'];
    $intZak = (int)$_POST["zak_id"];
    $query = "SELECT `max_points` FROM `questions` WHERE `question_number` = " . $_POST["question_number"];
    $result = $connect->query($query) or die("Chyba získání odpovědí");
    $maxPointsFromQuestion = ($result->fetch_object())->max_points;
    if ($intPoints <= $maxPointsFromQuestion){
        $query = "SELECT * FROM `answers` WHERE `question_number` = " . $intQNum . " AND `zak_id` = " . $intZak;
        $result = $connect->query($query) or die("Chyba získání odpovědí");
        $resultAlreadyExists = ($result->fetch_object())->zak_id;
        if(empty($resultAlreadyExists)){
            $query = "INSERT INTO `answers`(`question_number`, `zak_id`, `points`) VALUE ('" . $intQNum . "','" . $intZak . "','" . $intPoints . "')";
        }else{
            $query = "UPDATE `answers` SET `points`= " . $intPoints . " WHERE `zak_id` = " . $intZak . " AND `question_number` = " . $intQNum;
        }
        $result = $connect->query($query) or die("Chyba uložení odpovědí");
    }else{
        echo("Maximální počet bodů z otázky překročen. Toto je po dobu soutěže nejspíše zaviněno podváděním. Odstraňuji data žáka. DNF.");
        $query = "DELETE FROM `answers` WHERE `zak_id` = " . $_POST["zak_id"];
        $result = $connect->query($query) or die("Chyba smazání odpovědí.");
    }
}else{
    echo("Chyba formátu požadavku.");
}
?>