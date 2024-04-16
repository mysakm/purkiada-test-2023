<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test stránka</title>
</head>
<body>
    <input id="body">
    <button type="button" onclick="javascript:submit()">Odeslat</button>
    <script>
    function submit() {
        ajaxFormData = new FormData();
        ajaxFormData.append("query", 1);
        ajaxFormData.append("question_number", 1);
        ajaxFormData.append("zak_id", 1);
        ajaxFormData.append("points", parseInt(document.getElementById("body").value));
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.open("POST", "http://localhost/purkiada/data/hookAnswers.php");
        ajaxRequest.send(ajaxFormData);
        ajaxRequest.onreadystatechange = function(){
                    console.log(ajaxRequest.readyState);
                    console.log(ajaxRequest.status);
                    console.log(ajaxRequest.responseText);
                }
    }
    </script>
</body>
</html>