<?php
require("./sql.php");
if(isset($_POST["query"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("Připojení se nezdařilo.");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = "SELECT * FROM `answers` WHERE `question_number` = " . $_POST["question_number"] . " AND `zak_id` = " . $_POST["zak_id"];
    $result = $connect->query($query) or die("Chyba získání odpovědí");
    $resultAlreadyExists = ($result->fetch_object())->zak_id;
    if(empty($resultAlreadyExists)){
        $query = "INSERT INTO `answers`(`question_number`, `zak_id`, `points`) VALUES ('" . $_POST["question_number"] . "','" . $_POST["zak_id"] . "','" . $_POST["points"] . "')";
    }else{
        $query = "UPDATE `answers` SET `points`= ". $_POST["points"] ." WHERE `zak_id` = " . $_POST["zak_id"] . "AND `question_number` = " . $_POST["question_number"];
    }
    $result = $connect->query($query) or die("Chyba uložení odpovědí");
}
?>