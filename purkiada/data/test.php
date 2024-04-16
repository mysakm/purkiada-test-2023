<?php
require("./sql.php");
$connect = new mysqli($host, $user, $pass, $db) or die("Připojení se nezdařilo.");
$connect->set_charset("utf8") or die("Charset chyba.");
$query = "SELECT * FROM `permanent_logins` WHERE 1";
$result = $connect->query($query) or die("Chyba získání odpovědí");
while ($row = $result->fetch_object()) {
    echo('user_id ' . $row->user_id . ' username' . $row->username);
}
?>