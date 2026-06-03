<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../model/Helper.php';
$msg = $_GET['msg'] ?? "Ein unbekannter Fehler ist aufgetreten.";
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fehler aufgetreten</title>
    <style>
        .error-container
        {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 80vh;
            text-align: center;
            font-family: sans-serif;
        }
        .error-image
        {
            max-width: 400px;
            height: auto;
            margin-bottom: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div class="error-container">
        <img src="../images/sad_cat.jpg" alt="Fehler Bild" class="error-image">

        <h1> Nur auserwählte Menschen kommen auf diese Fehler-Seite... </h1>
        <p><?= htmlspecialchars($msg) ?></p>

        <br>
        <a href="index.php">Zurück zur Startseite</a>
    </div>

</body>
</html>
