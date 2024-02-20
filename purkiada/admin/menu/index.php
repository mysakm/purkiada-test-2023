<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require("../../data/sql.php");
if(!empty($_SESSION["access-key"])){
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = 'SELECT * FROM `session_management` WHERE `session_id` = "' . $_SESSION["access-key"] . '"';
    $result = $connect->query($query) or die("Fault");
    $connect->close();
    $resultUser = ($result->fetch_object())->user_id;
    if(empty($resultUser)){
        echo("<!DOCTYPE html><head><link href='./adminStyle.css' rel='stylesheet'></head><body style='background-color:black'><p class='scary'>YOU SHOULDN'T BE, HERE.</p></body></html>");
        header('HTTP/1.0 403 Forbidden', true, 403);
        header("Location: ../");
        die("Page access forbidden 403.");
    }else{
        menus();
    }
}else{
    echo("<!DOCTYPE html><head><link href='./adminStyle.css' rel='stylesheet'></head><body style='background-color:black'><p class='scary'>YOU SHOULDN'T BE, HERE.</p></body></html>");
    header('HTTP/1.0 403 Forbidden', true, 403);
    header("Location: ../");
    die("Page access forbidden 403.");
}
function menus(){
    require("../../data/sql.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./adminStyle.css">
    <title>Purkiáda - admin</title>
</head>
<body>
    <?php
        if (!empty($_POST["eventDate"])){
            $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
            $connect->set_charset("utf8") or die("Charset chyba.");
            $query = 'UPDATE `competition_status` SET `event_date`="' . $_POST["eventDate"] . '" WHERE 1';
            $connect->query($query) or die("Fault");
            $connect->close();
        }
        if(!empty($_GET["action"])){
            switch($_GET["action"]){
                case "reset":
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "DELETE FROM `zaci` WHERE 1";
                    $query2 = "ALTER TABLE `zaci` AUTO_INCREMENT = 1";
                    $query3 = "UPDATE `competition_status` SET `registration_open`=0,`competition_open`=0,`results_available`=0,`emails_sent`=0 WHERE 1";
                    $query4 = "DELETE FROM `questions` WHERE 1";
                    $connect->query($query) or die("Fault1");
                    $connect->query($query2) or die("Fault2");
                    $connect->query($query3) or die("Fault3");
                    $connect->query($query4) or die("Fault4");
                    $connect->close();
                    break;
                case "openRegistration":
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "UPDATE `competition_status` SET `registration_open`=1,`competition_open`=0,`results_available`=0 WHERE 1";
                    $result = $connect->query($query) or die("Fault1");
                    $connect->close();
                    break;
                case "sendEmails":
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "SELECT email FROM `zaci`";
                    $result = $connect->query($query) or die("Fault1");
                    $queryEmail = "SELECT email_template, event_date FROM `competition_status`";
                    $resultEmail = $connect->query($queryEmail) or die("Fault2");
                    $fetcher = ($resultEmail->fetch_object());
                    $emailToSend = $fetcher->email_template;
                    $eventDate = $fetcher->event_date;
                    $eventDateFormatted = strtotime($eventDate);
                    echo($eventDate);
                    $emailToSend = str_replace("{currentYear}",date("Y"),$emailToSend);
                    $emailToSend = str_replace("{eventYear}",date("Y", $eventDateFormatted),$emailToSend);
                    $emailToSend = str_replace("{eventDate}",date("d. m. Y", $eventDateFormatted),$emailToSend);
                    switch(date("N", $eventDateFormatted)){
                        case 1:
                            $emailToSend = str_replace("{eventDay}","pondělí",$emailToSend);
                            break;
                        case 2:
                            $emailToSend = str_replace("{eventDay}","úterý", $emailToSend);
                            break;
                        case 3:
                            $emailToSend = str_replace("{eventDay}","středa", $emailToSend);
                            break;
                        case 4:
                            $emailToSend = str_replace("{eventDay}","čtvrtek", $emailToSend);
                            break;
                        case 5:
                            $emailToSend = str_replace("{eventDay}","pátek", $emailToSend);
                            break;
                        case 6:
                            $emailToSend = str_replace("{eventDay}","sobota", $emailToSend);
                            break;
                        case 7:
                            $emailToSend = str_replace("{eventDay}","neděle", $emailToSend);
                            break;
                    }
                    $connect->close();
                    require('../../mailer/src/Exception.php');
                    require('../../mailer/src/PHPMailer.php');
                    while($row = $result->fetch_object()) {
                        $target = $row->email;
                        $mail = new PHPMailer();
                        $mail->IsSmtp();
                        $mail->SMTPDebug = 0;
        
                        $mail->SMTPAuth = true;
                        $mail->SMTPSecure = 'tls';
                        $mail->Host = $mailserver;
                        $mail->Port = $smtpport; 
                        $mail->IsHTML(true);
                        $mail->Username = $emailuser;
                        $mail->Password = $emailpass;
                        $mail->setFrom($emailuser);
                        $mail->CharSet = 'UTF-8';
                        $mail->Subject = "Detaily k Purkiádě";
                        $mail->Body = $emailToSend;
                        $mail->AddAddress($target);
                        $mail->Send();
                    }
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "UPDATE `competition_status` SET `emails_sent`=1 WHERE 1";
                    $result = $connect->query($query) or die("Fault1");
                    $connect->close();
                    break;
                case "openCompetition":
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "UPDATE `competition_status` SET `registration_open`=0,`competition_open`=1,`results_available`=0 WHERE 1";
                    $result = $connect->query($query) or die("Fault1");
                    $connect->close();
                    break;
                case "completeClose":
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "UPDATE `competition_status` SET `registration_open`=0,`competition_open`=0,`results_available`=0 WHERE 1";
                    $result = $connect->query($query) or die("Fault1");
                    $connect->close();
                    break;
                case "openResult":
                    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                    $connect->set_charset("utf8") or die("Charset chyba.");
                    $query = "UPDATE `competition_status` SET `registration_open`=0,`competition_open`=0,`results_available`=1 WHERE 1";
                    $result = $connect->query($query) or die("Fault1");
                    $connect->close();
                    break;
            }
        }
    ?>
    <form action="./setEmail.php" method="GET">
        <input type="hidden" name="action" value="setEmail">
        <input type="submit" value="Nastavit emailovou zprávu">
    </form>
    <?php
    $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
    $connect->set_charset("utf8") or die("Charset chyba.");
    $query = "SELECT * FROM `competition_status`";
    $query2 = "SELECT * FROM `questions` LIMIT 1";
    $query3 = "SELECT * FROM `zaci` LIMIT 1";
    $result = $connect->query($query) or die("Fault1");
    $result2 = $connect->query($query2) or die("Fault2");
    $result3 = $connect->query($query3) or die("Fault3");
    $connect->close();
    while($row = $result->fetch_object()) {
        $registration = $row->registration_open;
        $competition = $row->competition_open;
        $results = $row->results_available;
        $email = $row->emails_sent;
        $date = $row->event_date;
    }
    $questionsSet = !empty(($result2->fetch_object())->question_number);
    $readyForNextTime = empty(($result3->fetch_object())->zak_id);
    ?>
    <form action="" method="POST">
        <input type="date" name="eventDate" value=<?php echo('"' . $date . '"');?>>
        <input type="submit" value="Nastavit den soutěže">
    </form>
    <p>Stav:</p>
    <table style="table-layout:fixed; border-collapse:collapse">
        <tr>
            <td class=tableElements <?php
            if(!$registration and !$competition and !$results){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Pozastaveno</td>
            <td class=tableElements <?php
            if($registration){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Přihlašování</td>
            <td class=tableElements <?php
            if($competition){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Soutěžení</td>
            <td class=tableElements <?php
            if($results){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Výsledky</td>
        </tr>
        <tr>
            <td class=tableElements>
                <form action="" method="GET">
                    <input type="hidden" name="action" value="completeClose">
                    <input type="submit" value="Pozastavit" style="width:100%">
                </form>
            </td>
            <td class=tableElements>
                <form action="" method="GET">
                    <input type="hidden" name="action" value="openRegistration">
                    <input type="submit" value="Otevřít přihlášky" style="width:100%">
                </form>
            </td>
            <td class=tableElements>
                <form action="" method="GET">
                    <input type="hidden" name="action" value="openCompetition">
                    <input type="submit" value="Otevřít soutěž" style="width:100%">
                </form>
            </td>
            <td class=tableElements>
                <form action="" method="GET">
                    <input type="hidden" name="action" value="openResult">
                    <input type="submit" value="Zveřejnit výsledky" style="width:100%">
                </form>
            </td>
        </tr>
        <tr style="height:15px">
            <td></td>
        </tr>
        <tr>
            <td class=tableElements <?php
            if($readyForNextTime and !$registration and !$competition and !$results and !$email and empty($questionsSet)){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Resetováno</td>
            <td class=tableElements <?php
            if($email){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Emaily</td>
            <td class=tableElements <?php
            if(!empty($questionsSet)){
                echo('style="background-color: lightgreen; text-align:center;"');
            }else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Úlohy</td>
            <td class=tableElements <?php
            //if(!empty($questionsSet)){
                //echo('style="background-color: lightgreen; text-align:center;"');
            //}else{echo('style="background-color: #FF5559; text-align:center;"');}?>>Obodováno</td>
        </tr>
        <tr>
            <td class=tableElements>
                <form action="" method="GET">
                    <input type="hidden" name="action" value="reset">
                    <input type="submit" value="Zresetovat vše" style="width:100%">
                </form>
            </td>
            <td class=tableElements>
                <form action="" method="GET">
                    <input type="hidden" name="action" value="sendEmails">
                    <input type="submit" value="Rozeslat emaily" style="width:100%">
                </form>
            </td>
            <td class=tableElements>
                <form action="./setTasks.php" method="GET">
                    <input type="hidden" name="action" value="prepareTasks">
                    <input type="submit" value="Připravit úlohy" style="width:100%">
                </form>
            </td>
            <td class=tableElements>
                <form action="./givePoints.php" method="GET">
                    <input type="hidden" name="action" value="givePoints">
                    <input type="submit" value="Obodovat" style="width:100%">
                </form>
            </td>
        </tr>
    </table>
</body>
</html>
<?php
}
?>