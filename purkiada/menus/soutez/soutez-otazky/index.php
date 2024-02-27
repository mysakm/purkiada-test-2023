<?php
    session_start();
    require("../../../data/sql.php");
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = "SELECT `question_number`, `question`, `type` FROM `questions`";
    $result = $connect->query($query) or die("Fault1");
    $connect->close();
    ?>
    <form id="competition" action="./answersSubmit.php" enctype="multipart/form-data" method="POST">
    <?php
    while($row = $result->fetch_object()) {
        $questionNumber = $row->question_number;
        $questionText = $row->question;
        $type = $row->type;
        echo("<p>Otázka " . $questionNumber . "</p> <p>" . $questionText . "</p>");
        if( $type == "1") {
            echo('<textarea form="competition" name="answers[]"></textarea>');
        }
        elseif( $type == "2") {
            echo('<input type="file" name="Odpoved' . $questionNumber .'">');
        }
    }
    ?>
    </form>