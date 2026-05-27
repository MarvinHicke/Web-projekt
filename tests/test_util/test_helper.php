<?php
require_once __DIR__ . '/../../model/Helper.php';

echo "<h2>Test 1: Bild-Pfad:</h2>";

$testImages = [
    ['file' => '001010', 'folder' => 'works', 'size' => 'medium', 'desc' => 'Normales Bild (Works/Medium)'],
    ['file' => '2', 'folder' => 'artists', 'size' => 'square-thumb', 'desc' => 'Künstler Thumbnail'],
    ['file' => '9999', 'folder' => 'subjects', 'size' => 'square-medium', 'desc' => 'Fehlendes Bild'],
];

foreach ($testImages as $img) {
    $result = Helper::getImagePath($img['file'], $img['folder'], $img['size']);

    $status = (strpos($result, 'placeholder') !== false) ? "Platzhalter" : "Gefunden";

    echo "<strong>{$img['desc']}:</strong><br>";
    echo "Suche: <code>{$img['folder']} -> {$img['size']} -> {$img['file']}.jpg</code><br>";
    echo "Ergebnis: <code>$result</code> -> $status<br><br>";
}

echo "<h2>Test 2: Sicherheits-Check:</h2>";
$testID = "123";
echo (is_numeric($testID)) ? "ID '123' ist gültig.<br>" : "Fehler bei '123'.<br>";

$failID = "hallo";
echo (!is_numeric($failID)) ? "'hallo' korrekt als böse erkannt.<br>" : " Fehler bei 'hallo'.<br>";

