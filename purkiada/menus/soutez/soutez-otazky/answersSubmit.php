<?php
session_start();
session_start();
if (empty($_SESSION["username"])){
    die();
}
//enctype="multipart/form-data"
$target_dir = "../../../data/answers/Odpoved";
$fileNumber = 0;
$filesUploaded= [];
while(!empty($_FILES["files" . ($processedQuestionNumber)]["name"][$fileNumber])){
    $processedFile = $_FILES["files" . $processedQuestionNumber]["name"][$fileNumber];
    $fileType = strtolower(pathinfo($processedFile, PATHINFO_EXTENSION));
    $target_file = $target_dir . "_" . $_SESSION["username"] . "_" . ($fileNumber+1) . "." . $fileType;
    $filesUploaded[$fileNumber] = $target_file;
    move_uploaded_file($_FILES["files" . $processedQuestionNumber]["tmp_name"][$fileNumber], $target_file);
    $fileNumber++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purkiáda - Odevzdáno</title>
</head>
<body>
    <h1>Děkujeme za účast!</h1>
</body>
</html>