<?php

define('DBHOST', 'localhost');
define('DBNAME', 'art');
define('DBUSER', 'admin');
define('DBPASS', 'admin');

$dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
$user = DBUSER;
$password = DBPASS;

try 
{
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch(PDOException $ex) 
{
    exit('DB connection failed: ' . $ex->getMessage());
}


    $sql = "SELECT * FROM ARTISTS";
    $result = $pdo->query($sql);


?>

<!DOCTYPE html>
<html lang="de-de">
<head>
    <meta charset="UTF-8">
    <title>Datenbankzugriff</title>
</head>
<body>
    <h1>Datenbankzugriff</h1>
    <p>Diese Seite soll einen grundlegenden Zugang zu einer Datenbank verdeutlichen.</p>

    <table border="1">
        <tr>
            <th>Id</th>
            <th>Vorname</th>
            <th>Nachname</th>
            <th>Nationalit&auml;t</th>
        </tr>

<?php
while($artist = $result->fetch())
{
    $id = $artist["ArtistID"];
    $firstName = $artist["FirstName"];
    $lastName = $artist["LastName"];
    $nationality = $artist["Nationality"];

    echo "<tr>";
    echo "<td>$id</td>";
    echo "<td>$firstName</td>";
    echo "<td>$lastName</td>";

    switch ($nationality)
    { 
        case "France":
            $filename = "france.png";
            break;
        case "Spain":
            $filename = "spain.png";
            break;
        case "Netherlands":
            $filename = "netherlands.png";
            break;
        case "Norway":
            $filename = "norway.png";
            break;
        default:
            $filename = "default.png";
    }

    $fullFileName = "./images/".$filename;

    echo "<td><img src='$fullFileName' alt='$nationality' title='$nationality'></td>";
    echo "</tr>";
}   
            $pdo = null;
?>

    </table>
</body>
</html>