<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purkiáda - nastavení úvodní stránky</title>
</head>
<body>
    <form action="" id="mainPageForm" type="POST">
        <textarea form="mainPageForm" rows="50" cols="150">
            <?php
            echo(readfile("../../index.php"));
            ?>
        </textarea>
    </form>
</body>
</html>