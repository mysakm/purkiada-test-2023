<?php
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
    <title>Purkiáda - výtisk nejlepších</title>
</head>
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
?>