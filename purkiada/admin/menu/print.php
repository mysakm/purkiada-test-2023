<?php
    require("../../data/sql.php");
    $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT `zaci`.`login`, SUM(points) AS soucet FROM `answers` INNER JOIN `zaci` ON `answers`.`zak_id` = `zaci`.`zak_id` WHERE 1 GROUP BY `answers`.`zak_id` ORDER BY `soucet` DESC LIMIT 10';
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
        $competitors[] = array(
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
            while (isset($competitors[$xOffset+1]) and $competitors[$xOffset]['pointSum'] == $competitors[$xOffset+1]['pointSum']) {
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
        echo('<tr><td style="border:black; border-style: solid; border-collapse: collapse">' . $competitor['login'] . '</td><td style="border:black; border-style: solid; border-collapse: collapse">' . $competitor['place'] . '</td><td style="border:black; border-style: solid; border-collapse: collapse">' .  $competitor['pointSum'] . '</td></tr>');
    }
    ?>
    </table>
</body>
</html>
<?php
?>