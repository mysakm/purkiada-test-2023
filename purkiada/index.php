
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Purkiáda</title>
</head>
<?php
$isMobile = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));
if ($isMobile and !isset($_POST["switch"])) {
    ?>
    <p>Ahoj! Zjistili jsme, že používáš mobilní zařízení. Chceš přepnout na verzi pro telefony?</p>
    <a href="./m/index.php">
        <button type="button">Ano</button>
    </a>
    <form action="" method="POST">
        <input type="hidden" name="switch" value="No">
        <input type="submit" value="Ne">
    </form>
    <?php
}else{
?>
<body>
    <div class="banner" style="border-bottom-width: 3px; border-bottom-color:lightgray; border-bottom-style:solid;">
        <a href="./" height="60px" style="padding-left:5%">
            <img src="./images/purkiada.png" height="60px">
        </a>
        <a href="./" class="banner-redirect">
            <p>Úvodní stránka</p>
        </a>
        <a href="./menus/predchozi-rocniky" class="banner-redirect">
            <p>Předchozí ročníky</p>
        </a>
        <?php
        require("./data/sql.php");
        $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $query = "SELECT * FROM `competition_status`";
        $result = $connect->query($query) or die("Fault1");
        $connect->close();
        while($row = $result->fetch_object()) {
            $registration = $row->registration_open;
            $competition = $row->competition_open;
            $results = $row->results_available;
        }
        if($competition == 1){
            ?>
            <a href="./menus/soutez" class="banner-redirect">
                <p>Soutěžit</p>
            </a>
            <?php
        }elseif($results == 1){

        }else{
            ?>
            <a href="./menus/prihlaska" class="banner-redirect">
                <p>Přihlásit se</p>
            </a>
            <?php
        }
        ?>
        <a href="./menus/informace" class="banner-redirect">
            <p>Informace</p>
        </a>
    </div>
    <div class="main"> <!-- v této třídě je hlavní tělo stránky, pokud používáte zpracovaný header -->
    </div>
</body>
<?php
}
?>
</html>