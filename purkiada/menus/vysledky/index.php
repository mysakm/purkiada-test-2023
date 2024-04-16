<?php
require("../../data/sql.php");
$connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
$connect->set_charset("utf8") or die("Charset chyba.");
$query = "SELECT * FROM `competition_status`";
$result = $connect->query($query) or die("Fault1");
$connect->close();
while($row = $result->fetch_object()) {
    $results = $row->results_available;
}
if ($results != 1){
    die("403 Forbidden");
}else{
    require("../../data/sql.php");
    $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT `zaci`.`login`, SUM(points) AS soucet FROM `answers` INNER JOIN `zaci` ON `answers`.`zak_id` = `zaci`.`zak_id` WHERE 1 GROUP BY `answers`.`zak_id` LIMIT 10';
    $result = $connect->query($query) or die("Fault");
    $connect->close();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purkiáda - výsledky</title>
</head>
<h2>Výsledky soutěže:</h2>
<p>Gratulujeme všem vítězům a přejeme hodně štěstí na přijímačkách poraženým.</p>
<body>
    <table style="border:black; border-style: solid; border-collapse: collapse">
        <?php
    while ($row = $result->fetch_object()) {
        echo('<tr><td style="border:black; border-style: solid; border-collapse: collapse">' . $row->login . '</td><td style="border:black; border-style: solid; border-collapse: collapse">' . $row->soucet . '</td></tr>');
    }
    ?>
    </table>
</body>
</html>
<?php
}
?>