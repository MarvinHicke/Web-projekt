<?php
// Initialisierung und Hilfsfunktionen laden.
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../model/Helper.php';

// Fehlermeldung aus der URL lesen oder eine Standardmeldung verwenden.
$msg = $_GET['msg'] ?? "Ein unbekannter Fehler ist aufgetreten.";
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fehler aufgetreten</title>

    <style>
        /* Zentriert den Fehlerbereich horizontal und vertikal auf der Seite. */
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

        /* Begrenzte Darstellung des Fehlerbildes mit abgerundeten Ecken. */
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

<!-- Fehleranzeige mit Bild, Meldung und Rücklink. -->
<div class="error-container">
    <img src="../images/sad_cat.jpg" alt="Fehler Bild" class="error-image">

    <h1> Nur auserwählte Menschen kommen auf diese Fehler-Seite ... </h1>

    <!-- Sichere Ausgabe der Fehlermeldung. -->
    <p><?= htmlspecialchars($msg) ?></p>

    <br>

    <!-- Link zurück zur Startseite. -->
    <a href="/index.php">Zurück zur Startseite</a>
</div>

</body>
</html>