<?php
require_once 'includes/init.php';
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Browse Artists</title>
    <meta name="Browse all Artists" content="Das ist die Seite die alle enthaltenen Artists anzeigt.">
</head>

<body>
<h1>Browse Artists</h1>
<p>
    Hier findet man eine Listenübersicht aller Artists. <br>
    Diese lassen sich nach Name und Vorname sortieren (aufsteigend oder absteigend).
</p>

<!-- List Container -->
<?php include 'C:\xampp\htdocs\Web-projekt\components\list-container-start.php';?>

<!-- Artist card -->
<?php include 'C:\xampp\htdocs\Web-projekt\components\artist-card.php';?>

<!-- wie einbauen??? -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <div class="col">
        <div class="card">
            <div class="card-body"></div>
        </div>
    </div>

</div>


<!-- Components in Models einpflegen und nutzbar machen -->


    <thead>
    <tr>
        <th>Vorname</th>
        <th>Bild</th>
        <th>Link</th>
    </tr>
    </thead>

    <tbody>
    <tr>
        <td>Nachname 1</td>
        <td>Vorname 1</td>
        <td><img src="https://www.ri-fa.de/wp-content/uploads/2022/10/ri-fa-platzhalter-produkt-1500x1500-1.jpg" width="50" alt="Platzhalter Bilder"></td>
        <td><a href="artist1.php">Link zur zukünftigen Seite</a></td>
    </tr>
    <tr>
        <td>Nachname 2</td>
        <td>Vorname 2</td>
        <td><img src="https://www.ri-fa.de/wp-content/uploads/2022/10/ri-fa-platzhalter-produkt-1500x1500-1.jpg" width="50" alt="Platzhalter Bilder"></td>
        <td><a href="artist2.php">Link zur zukünftigen Seite</a></td>
    </tr>
    <tr>
        <td>Nachname 3</td>
        <td>Vorname 3</td>
        <td><img src="https://www.ri-fa.de/wp-content/uploads/2022/10/ri-fa-platzhalter-produkt-1500x1500-1.jpg" width="50" alt="Platzhalter Bilder"></td>
        <td><a href="artist3.php">Link zur zukünftigen Seite</a></td>
    </tr>
    <tr>
        <td>Nachname 4</td>
        <td>Vorname 4</td>
        <td><img src="https://www.ri-fa.de/wp-content/uploads/2022/10/ri-fa-platzhalter-produkt-1500x1500-1.jpg" width="50" alt="Platzhalter Bilder"></td>
        <td><a href="artist4.php">Link zur zukünftigen Seite</a></td>
    </tr>
    <tr>
        <td>Nachname 5</td>
        <td>Vorname 5</td>
        <td><img src="https://www.ri-fa.de/wp-content/uploads/2022/10/ri-fa-platzhalter-produkt-1500x1500-1.jpg" width="50" alt="Platzhalter Bilder"></td>
        <td><a href="artist5.php">Link zur zukünftigen Seite</a></td>
    </tr>
    </tbody>
</table>

</body>
