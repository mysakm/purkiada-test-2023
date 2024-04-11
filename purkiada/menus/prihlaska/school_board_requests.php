<?php
require("../../data/sql.php");
if(isset($_POST["query"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("Připojení se nezdařilo.");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $schoolCleared = str_replace("'",'',$_POST["query"]);
    $query = "SELECT full_name, IZO FROM `schools` WHERE `full_name` LIKE '%" . $schoolCleared . "%' OR `IZO` LIKE '%" . $schoolCleared . "%' LIMIT 10;";
    $result = $connect->query($query) or die("Chyba ziskání škol.");
    while($row = $result->fetch_object()) {
        $data[] = array(
            'fullname' => $row->full_name,
            'izo' => $row->IZO
        );
    }
    echo(json_encode($data));
}
?>