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
    <title>Purkiáda - přidat úlohy</title>
</head>
<body>
    <a href="./index.php"><p style="background-color:lightgray; width:30px">Zpět</p></a>
    <form action="" id="set-tasks" enctype="multipart/form-data" method="POST">
        <?php
        if(isset($_POST["task"])){
            $processedQuestionNumber = 0;
            $stringPreparation = "";
            while(!empty($_POST["task"][$processedQuestionNumber])) {
                if ($stringPreparation != "") {
                    $stringPreparation = $stringPreparation . ",";
                }
                $stringPreparation = $stringPreparation . "(" . ($processedQuestionNumber + 1) . ', "' . str_replace('"', "'", $_POST["task"][$processedQuestionNumber]) . '", ' . $_POST["type". ($processedQuestionNumber+1)] . ', ' . $_POST["points"][$processedQuestionNumber] . ')';
                $processedQuestionNumber++;
                $fileNumber = 0;
                $filesUploaded= [];
                while(!empty($_FILES["files" . ($processedQuestionNumber)]["name"][$fileNumber])){
                    $processedFile = $_FILES["files" . $processedQuestionNumber]["name"][$fileNumber];
                    $fileType = strtolower(pathinfo($processedFile, PATHINFO_EXTENSION));
                    $target_file = "../../data/questions/Otazka" . $processedQuestionNumber . "_" . ($fileNumber+1) . "." . $fileType;
                    $filesUploaded[$fileNumber] = $target_file;
                    move_uploaded_file($_FILES["files" . $processedQuestionNumber]["tmp_name"][$fileNumber], $target_file);
                    $fileNumber++;
                }
                foreach ($filesUploaded as $downloadable) {
                    $downloadableEdit = "../" . $downloadable;
                    $stringPreparation = preg_replace("({file})", '<a href=' . $downloadableEdit . ' download>Soubor ke stažení</a>', $stringPreparation, 1);
                }
            }
            $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
            $connect->set_charset("utf8") or die("Charset chyba.");
            $query = "DELETE FROM `questions` WHERE 1";
            $result = $connect->query($query) or die("Fault2");
            $query = "INSERT INTO `questions`(`question_number`, `question`, `type`, `max_points`) VALUES " . $stringPreparation;
            $result = $connect->query($query) or die("Fault3");
            $connect->close();
        }
        $connect = new mysqli($host, $anothauser, $anothapass, $db) or die("pripojeni se nezdarilo");
        $connect->set_charset("utf8") or die("Charset chyba.");
        $query = "SELECT * FROM `questions`";
        $result = $connect->query($query) or die("Fault4");
        $connect->close();
        while ($row = $result->fetch_object()) {
            $questionNumber = $row->question_number;
            $question = $row->question;
            $type = $row->type;
            $points = $row->max_points;
            echo('<p>Úloha ' . $questionNumber . '</p> <textarea form="set-tasks" onchange="checkForFileTag(' . $questionNumber . ')" name="task[]">' . $question . '</textarea><br>');
            for ($i = 0; $i < substr_count($question, "{file}"); $i++) {
                echo("<input type='file' name='files". $questionNumber . "[]'>");
            }
           /* echo("<p>Typ odpovědi:</p> ");
            if ($type == '1') {
                echo('<label for="text' . $questionNumber . '">Text</label><input type="radio" id="text' . $questionNumber . '" name="type' . $questionNumber . '" value="1" checked="checked">');
            }else{
                echo('<label for="text">Text</label><input type="radio" id="text' . $questionNumber . '" name="type' . $questionNumber . '" value="1">');
            }
            echo("<br>");
            if ($type == '2') {
                echo('<label for="upload' . $questionNumber . '">Nahrát soubor</label><input type="radio" id="upload' . $questionNumber . '" name="type' . $questionNumber . '" value="2" checked="checked">');
            }else{
                echo('<label for="upload' . $questionNumber . '">Nahrát soubor</label><input type="radio" id="upload' . $questionNumber . '" name="type' . $questionNumber . '" value="2">');
            }*/
            if ($type == '3') {
                echo('<label for="hook' . $questionNumber . '">Webhook</label><input type="radio" id="hook' . $questionNumber . '" name="type' . $questionNumber . '" value="3" checked="checked">');
            }else{
                echo('<label for="hook' . $questionNumber . '">Webhook</label><input type="radio" id="hook' . $questionNumber . '" name="type' . $questionNumber . '" value="3">');
            }
            echo('<p> Maximální množství bodů:</p> <input type="number" name="points[]" value="' . $points . '">');
        }
        if(empty($questionNumber)){$questionNumber = 0;}
        echo('<input type="hidden" id="questionCount" name="questionCount" value="' . $questionNumber . '">');
        ?>
        <br>
        <p>HTML tagy mohou být použity k vytváření forem ;3</p>
        <br>
        <button type="button" onclick="addQuestion()">Přidat úkol</button>
        <input type="submit" value="Uložit">
    </form>
    <script>
    function addQuestion() {
        currentquestion = document.getElementById("questionCount");
        form = document.getElementById("set-tasks"); // gets the needed elements

        title = document.createElement("p");
        questionNumber = parseInt(currentquestion.getAttribute("value"));
        let currentQuestionNumber = questionNumber + 1;
        title.innerText = "Úloha " + currentQuestionNumber;
        currentquestion.setAttribute("value", currentQuestionNumber);
        form.insertBefore(title, currentquestion); // creates task text

        textarea = document.createElement("textarea");
        textarea.setAttribute("form", "set-tasks");
        textarea.setAttribute("name", "task[]");
        textarea.setAttribute("onchange", "checkForFileTag(" + currentQuestionNumber + ")");
        form.insertBefore(textarea, currentquestion);
        form.insertBefore(document.createElement("br"), currentquestion); // creates textarea to fill in task info

        answerP = document.createElement("p");
        answerP.innerText = "Typ odpovědi:";
        form.insertBefore(answerP, currentquestion); // creates answer text

        /*labelText = document.createElement("label");
        labelText.setAttribute("for", "text" + currentQuestionNumber);
        labelText.innerText="Text";
        form.insertBefore(labelText, currentquestion);
        textRadio = document.createElement("input");
        textRadio.setAttribute("id", "text" + currentQuestionNumber);
        textRadio.setAttribute("type", "radio");
        textRadio.setAttribute("name", "type" + currentQuestionNumber);
        textRadio.setAttribute("value", "1");
        form.insertBefore(textRadio, currentquestion);
        form.insertBefore(document.createElement("br"), currentquestion); // radio button for text

        labelUpload = document.createElement("label");
        labelUpload.setAttribute("for", "upload" + currentQuestionNumber);
        labelUpload.innerText="Nahrát soubor";
        form.insertBefore(labelUpload, currentquestion);
        uploadRadio = document.createElement("input");
        uploadRadio.setAttribute("id", "upload" + currentQuestionNumber);
        uploadRadio.setAttribute("type", "radio");
        uploadRadio.setAttribute("name", "type" + currentQuestionNumber);
        uploadRadio.setAttribute("value", "2");
        form.insertBefore(uploadRadio, currentquestion);
        form.insertBefore(document.createElement("br"), currentquestion); // radio button for upload*/

        labelHook = document.createElement("label");
        labelHook.setAttribute("for", "hook" + currentQuestionNumber);
        labelHook.innerText="Webhook";
        form.insertBefore(labelHook, currentquestion);
        hookRadio = document.createElement("input");
        hookRadio.setAttribute("id", "hook" + currentQuestionNumber);
        hookRadio.setAttribute("type", "radio");
        hookRadio.setAttribute("name", "type" + currentQuestionNumber);
        hookRadio.setAttribute("value", "3");
        form.insertBefore(hookRadio, currentquestion);
        form.insertBefore(document.createElement("br"), currentquestion); // radio button for webhook

        answerP = document.createElement("p");
        answerP.innerText = "Maximální množství bodů:";
        form.insertBefore(answerP, currentquestion); // creates answer text

        answerTextarea = document.createElement("input");
        answerTextarea.setAttribute("type", "number");
        answerTextarea.setAttribute("name", "points[]");
        form.insertBefore(answerTextarea, currentquestion); // creates input that says how many points can a question earn
    }
    function checkForFileTag(questionNumberInForm) {
        let radioButton = document.getElementById("text" + questionNumberInForm);
        form = document.getElementById("set-tasks");
        let numberOfUploads = 0; // need to compensate for <br>, <p> and <label>
        let checkPreviousSibling = radioButton.previousSibling;
        while(checkPreviousSibling.nodeName.toLowerCase() != "textarea"){
            if(checkPreviousSibling.nodeName.toLowerCase() == "input"){
            numberOfUploads++;
            }
            checkPreviousSibling =checkPreviousSibling.previousSibling;
        }
        let testedTextarea = checkPreviousSibling;
        console.log(numberOfUploads); // number of already active file inputs
        let starter = 0;
        let numberOfFileTags = 0;
        while(testedTextarea.value.indexOf("{file}", starter) != -1){
            starter = testedTextarea.value.indexOf("{file}", starter) + 1;
            numberOfFileTags++;
        }
        console.log(numberOfFileTags);
        for (numberOfUploads = numberOfUploads; numberOfUploads < numberOfFileTags; numberOfUploads++) {
            uploadInput = document.createElement("input");
            uploadInput.setAttribute("type", "file");
            uploadInput.setAttribute("name", "files" + questionNumberInForm + "[]");
            form.insertBefore(uploadInput, testedTextarea.nextSibling.nextSibling); // just so I can put it before the radio 
        }
    }
    </script>
</body>
</html>
<?php
}
?>