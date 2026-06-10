<?php

require_once __DIR__ . '/../../includes/dbaccess.php';
require_once __DIR__ . '/../../repositories/subjectRepository.php';

$db = new dbaccess();
$db->connect();
$subjectRepo = new subjectRepository($db);

echo "<h1>Repository Test: Subject</h1>";

$all = $subjectRepo->findAll();

echo "<p>Methode: findAll()</p>";
if (is_array($all) && count($all) > 0)
{
    echo "Ergebnis (Anzahl): " . count($all) . " Subjects gefunden - erfolgreich<br>";
    $firstSubject = $all[0];
    $id = $firstSubject->getSubjectid();
} else
{
    echo "Ergebnis: Keine Subjects gefunden - FEHLGESCHLAGEN<br>";
    $id = 1;
}

echo "<p>Methode: getById()</p>";
$subject = $subjectRepo->getById($id);
if ($subject && $subject->getSubjectid() === $id)
{
    echo "Ergebnis: Thema mit ID " . $id . " (" . $subject->getSubjectname() . ") erfolgreich geladen - erfolgreich<br>";
} else
{
    echo "Ergebnis: Thema mit ID " . $id . " konnte nicht geladen werden - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getAllForBrowse()</p>";
$browse = $subjectRepo->getAllForBrowse();
if (is_array($browse) && count($browse) > 0)
{
    echo "Ergebnis (Anzahl): " . count($browse) . " Subjects sortiert gefunden - erfolgreich<br>";
} else
{
    echo "Ergebnis: Keine Subjects gefunden - FEHLGESCHLAGEN<br>";
}

echo "<p>Methode: getSubjectsForArtwork()</p>";
$artworkId = 1;
$artworkSubjects = $subjectRepo->getSubjectsForArtwork($artworkId);
if (is_array($artworkSubjects))
{
    echo "Ergebnis (Anzahl): " . count($artworkSubjects) . " Subjects für Artwork ID " . $artworkId . " gefunden - erfolgreich<br>";
} else
{
    echo "Ergebnis: Fehler bei der Abfrage - FEHLGESCHLAGEN<br>";
}

$db->close();