<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style.css">
    <title>Purkiáda - přihláška</title>
</head>
<body>
    <?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require('../../mailer/src/Exception.php');
    require('../../mailer/src/PHPMailer.php');
    require('../../mailer/src/SMTP.php');
    require('../../data/sql.php');
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
    if($registration == 1){
        require("../banner.php");
        banner();
        if(empty($_POST['name']) and empty($_POST['surname']) and empty($_POST['email']) and empty($_POST['school'])){
            login_sheet(false, null);
        }elseif (empty($_POST['name']) or empty($_POST['surname']) or empty($_POST['email']) or empty($_POST['school'])) {
            if(empty($_POST['name'])){
                login_sheet(true, "name");
            }elseif (empty($_POST['surname'])) {
                login_sheet(true, "surname");
            }elseif (empty($_POST['email'])) {
                login_sheet(true, "email");
            }elseif (empty($_POST['school'])) {
                login_sheet(true, "school");
            }
        ?>
        <?php
        }else{
            $connect = new mysqli($host, $user, $pass, $db) or die("Připojení se nezdařilo.");
            $connect->set_charset("utf8") or die("Charset chyba.");
            $query = "SELECT COUNT(*) as count FROM `schools` WHERE `full_name` = '" . $_POST['school'] . "'";
            $result = $connect->query($query) or die("Chyba ziskani škol.");
            $count;
            while($row = $result ->fetch_object()) {
                $count = $row->count;
            }
            if(!$count){
                login_sheet(true, "school");
            }
            elseif (empty($_POST['correct-code']) or $_POST['correct-code'] != $_POST['check-code']){
                //INSERT INTO `zaci`(`zak_id`, `name`, `surname`, `email`, `city`, `schools_id`) VALUE (NULL, "a", "b", "c", "d", 69752)
                //ALTER TABLE zaci AUTO_INCREMENT = 1
                $code = random_int(10000, 99999);
                $msg = "Právě nám přišel požadavek o registraci na rezervačním systému dne firem." . "<br>" . "Kód pro registraci je: " . $code . "<br>" . "Pokud jsi nezadal tento požadavek, nepřeposílej tento kód nikomu jinému, je možné, že ti chtějí vzít účet.";
                $msg = wordwrap($msg,70);
                $mailto = $_POST['email'];
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
                $mail->Subject = "Požadavek o registraci na den firem";
                $mail->Body = $msg;
                $mail->AddAddress($mailto);
                $mail->Send();
                ?>
                <div class="main">
                    <form action="" method="POST">
                        <input type="hidden" name="name" value="<?php echo($_POST['name']); ?>">
                        <input type="hidden" name="surname" value="<?php echo($_POST['surname']); ?>">
                        <input type="hidden" name="email" value="<?php echo($_POST['email']); ?>">
                        <input type="hidden" name="school" value="<?php echo($_POST['school']); ?>">
                        <input type="hidden" name="correct-code" value="<?php echo($code); ?>">
                        <h1>Přihlášení (2/2)</h1>
                        <p>Poslali jsme ti na e-mail ověřovací kód, zadej jej prosím zde:</p>
                        <input type="text" name="check-code">
                        <input type="Submit" value="Odeslat">
                    </form>
                </div>
                <?php
            }else{
                $connect = new mysqli($host, $user, $pass, $db) or die("pripojeni se nezdarilo");
                $connect->set_charset("utf8") or die("Charset chyba.");
                $query1 = "SELECT `IZO` FROM `schools` WHERE full_name = '" . $_POST['school'] ."'";
                $result = $connect->query($query1) or die ("chyba ziskani info IZO");
                $query = 'INSERT INTO `zaci`(`zak_id`, `name`, `surname`, `email`, `schools_id`) VALUE (NULL, "' . $_POST['name'] . '", "' . $_POST['surname'] . '", "' . $_POST['email'] . '", ' . $result->fetch_object()->IZO . ')';
                $result = $connect->query($query) or die("chyba ziskani info 2" . print_r($connect));
                $connect->close();
                ?>
                <div class="main">
                    <h1>Děkujeme!</h1>
                    <p>Bližší informace ti přijdou do emailu několik dní před soutěží.</p>
                </div>
                <?php
            }
        }
    }else{
        require("../banner.php");
        banner()
        ?>
        <div class="main">
            <p>Litujeme, ale přihlášky jsou momentálně uzavřeny.</p>
        </div>
        <?php
    }
    ?>
</body>
</html>
<?php
function login_sheet($isError, $where){
    require("../../data/sql.php");
    ?>
    <div class="main">
        <form action="" method="POST">
            <h1>Přihlášení (1/2)</h1>
            <table>
                <tr>
                    <td>
                        <p <?php if($where == "name"){echo('style="color:red"');}?>>Jméno:</p>
                    </td>
                    <td>
                        <input id="name" type="text" name="name">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p <?php if($where == "surname"){echo('style="color:red"');}?>>Příjmení:</p>
                    </td>
                    <td>
                        <input id="surname" type="text" name="surname">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p <?php if($where == "email"){echo('style="color:red"');}?>>E-Mail:</p>
                    </td>
                    <td>
                        <input id="email" type="text" name="email">
                    </td>
                </tr>
                <tr>
                    <td>
                        <p <?php if($where == "school"){echo('style="color:red"');}?>>Škola:</p>
                    </td>
                    <td>
                        <input id="school" type="text" name="school" list="schools" placeholder="Zadej plné jméno školy nebo IZO zde.." onkeyup="javascript:fetchData(this.value)">
                        <span id="search_results"></span>
                    </td>
                </tr>
            </table>
            <p>Přihlášením souhlasíš s našimi pravidly GDPR, které jsou ke zhlédnutí zde: <a style="color:blue; text-decoration:underline;" onclick="gdprPopup()">GDPR</a></p>
            <input type="submit" value="Odeslat">
        </form>
    </div>
    <script>
        function gdprPopup() {
            alert("1. Dávám souhlas se s evidencí osobních údajů podle zák.č. 101/2000Sb., v platném znění. Souhlasím s tím, aby organizační tým soutěže evidoval mé osobní údaje, případně údaje mého dítěte, poskytnuté v souvislosti s účastí v soutěži. Jedná se o tyto údaje: jméno, příjmení, email, škola, kterou v současnosti navštěvuji.\n 2. Tyto údaje budou použity pouze pro účely evidence soutěžících, nebudou nikomu poskytnuty a po skončení a vyhodnocení soutěže budou smazány.\n3. Souhlasím také s pořizováním a uveřejněním hromadných fotografií ze soutěže za účelem dokumentace a propagace soutěže na webu školy.\n4. Prohlašuji, že jsem byl/a řádně informován/a o všech skutečnostech dle ustanovení § 11 zákona č. 101/2000Sb., v platném znění.");
        }
        function fetchData(query) {
            if (query.length > 4){
                ajaxFormData = new FormData();
                ajaxFormData.append("query", query);
                ajaxRequest = new XMLHttpRequest();
                ajaxRequest.open("POST", "./school_board_requests.php");
                ajaxRequest.send(ajaxFormData);
                ajaxRequest.onreadystatechange = function(){
                    if(ajaxRequest.readyState == 4 && ajaxRequest.status == 200){
                        html = '<div class="list-group">';
                        if (ajaxRequest.responseText.charAt(0) != "<"){
                            response = JSON.parse(ajaxRequest.responseText);
                        }
                        if (response.length > 0){
                            for (var count = 0; count < response.length; count++){
                                html += '<a href="#" onclick="getText(this)"><p>' + response[count].fullname + "</a></p>";
                            }
                        }else{
                            html += "<p>Nemohli jsme najít žádný záznam..</p>";
                        }
                        html += "</div>";
                        document.getElementById("search_results").innerHTML = html;
                    }
                }
            }else{
                document.getElementById('search_results').innerHTML = '';
            }
        }
        function getText(event){
            dataToEnter = event.textContent;
            document.getElementById("school").value = dataToEnter;
            document.getElementById("search_results").innerHTML = "";
        }
    </script>
    <?php if($isError) {
        print('<p style="color:red">Některá z hodnot nebyla zadána, prosím, zkontrolujte, zda-li je vše vyplněno</p>');
    }
}
?>