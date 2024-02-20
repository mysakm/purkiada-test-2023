<?php
function banner(){
    ?>
    <div class="banner" style="border-bottom-width: 3px; border-bottom-color:lightgray; border-bottom-style:solid;">
        <a href="../.." height="60px" style="padding-left:5%">
            <img src="../../images/purkiada.png" height="60px">
        </a>
        <a href="../.." class="banner-redirect">
            <p>Úvodní stránka</p>
        </a>
        <a href="../predchozi-rocniky" class="banner-redirect">
            <p>Předchozí ročníky</p>
        </a>
        <?php
        require("../../data/sql.php");
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
            <a href="../soutez" class="banner-redirect">
                <p>Soutěžit</p>
            </a>
            <?php
        }elseif($results == 1){

        }else{
            ?>
            <a href="../prihlaska" class="banner-redirect">
                <p>Přihlásit se</p>
            </a>
            <?php
        }
        ?>
        <a href="../informace" class="banner-redirect">
            <p>Informace</p>
        </a>
    </div>
    <?php
}
?>