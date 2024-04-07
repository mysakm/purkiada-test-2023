<?php
session_start();
require("../../data/sql.php");
if(!empty($_SESSION["access-key"])){
    $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
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
    <title>Purkiáda - upravit email</title>
</head>
<?php
    if(isset($_POST["email-text"])){
        $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $submitEmailText = str_replace("\r\n", "<br>", $_POST['email-text']);
        $submitEmailSubject = str_replace("\r\n", "<br>", $_POST['title']);
        switch ($_GET['action']) {
            case 'setEmailsBefore':
                $query = 'UPDATE `competition_status` SET `upcoming_email_template`= "' . $submitEmailText . '",`upcoming_email_title`= "' . $submitEmailSubject . '" WHERE 1';
                break;
            
            case 'setEmailsLogins':
                $query = 'UPDATE `competition_status` SET `login_email_template`= "' . $submitEmailText . '",`login_email_title`= "' . $submitEmailSubject . '" WHERE 1';
                break;
            
            case 'setEmailsAfterwards':
                $query = 'UPDATE `competition_status` SET `result_email_template`= "' . $submitEmailText . '",`result_email_title`= "' . $submitEmailSubject . '" WHERE 1';
                break;
        }
        $result = $connect->query($query) or die("Fault1");
        $connect->close();
    }
?>
<body>
    <a href="./index.php"><p style="background-color:lightgray; width:30px">Zpět</p></a>
    <form action="" id="emailForm" method="POST">
        <?php
        $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $query = "SELECT upcoming_email_template, login_email_template, result_email_template, upcoming_email_title, login_email_title, result_email_title FROM `competition_status`";
        $result = $connect->query($query) or die("Fault1");
        $connect->close();
        while($row = $result->fetch_object()) {
            $beforeEmailText = $row->upcoming_email_template;
            $loginEmailText = $row->login_email_template;
            $resultEmailText = $row->result_email_template;
            $beforeEmailSubject = $row->upcoming_email_title;
            $loginEmailSubject = $row->login_email_title;
            $resultEmailSubject = $row->result_email_title;
        }
        switch ($_GET['action']) {
            case 'setEmailsBefore':
                $emailText = $beforeEmailText;
                $emailSubject = $beforeEmailSubject;
                break;
            
            case 'setEmailsLogins':
                $emailText = $loginEmailText;
                $emailSubject = $loginEmailSubject;
                break;
            
            case 'setEmailsAfterwards':
                $emailText = $resultEmailText;
                $emailSubject = $resultEmailSubject;
                break;
        }
        $emailText = str_replace("<br>", "\n", $emailText);
    ?>
    <table>
        <tr>
            <td>
                Předmět:
            </td>
            <td>
                <input type="text" name="title" value="<?php echo($emailSubject) ?>">
            </td>
        <tr>
            <td>
                Text:
            </td>
            <td>
                <textarea form="emailForm" name="email-text" rows="20" cols="75"><?php
                    echo($emailText);
                    ?>
                </textarea>
            </td>
        </tr>
    </table>
        <input type="hidden" name="action" value="<?php echo($_GET['action'])?>">
        <input type="submit" value="Nastavit">
    </form>
    <p>Speciální znaky:</p>
    <p>{currentYear} - doplní rok podle aktuálního času</p>
    <p>{eventDate} - doplní datum podle dne soutěže (nastavitelné v hlavním admin menu, formát d. m. y)</p>
    <p>{eventYear} - doplní rok podle dne soutěže</p>
    <p>{eventDay} - doplní jméno dne v týdnu podle dne soutěže</p>
    <p>{login} - doplní přihlašovací jméno žáka</p>
    <p>{password} - doplní přihlašovací heslo žáka</p>
</body>
</html>
<?php
}?>