<?php

/**
 * Hilfsklasse mit  statischen Funktionen für das gesamte Projekt
 */
class Helper
{

    /**
     * Überprüft, ob eine übergebene ID gültig und numerisch ist
     * Falls nicht, dann Weiterleitung zur Fehlerseite
     *
     * @param int $id Die zu überprüfende ID (z. B. aus der URL).
     */
    public static function checkQueryId($id)
    {
        if (!isset($id) || !is_numeric($id))
        {
            header("Location: /Web-projekt/pages/error.php");
            exit;
        }
    }

    /**
     * Generiert den relativen Pfad zu einem Bild oder gibt einen Platzhalter zurück, falls die Datei nicht existiert
     *
     * @param string $filename Der Dateiname des Bildes (bitte ohne Endung :>)
     * @param string $subfolder Der Unterordner wie 'artworks' oder 'artists'
     * @param string $size Die gewünschte Bildgröße (Standard ist medium!).
     * @return string Der relative Pfad zum Bild oder zum Platzhalter
     */
    public static function getImagePath($filename, $subfolder, $size = 'medium')
    {

        $relPath = "images/" . $subfolder . "/" . $size . "/" . $filename . ".jpg";

        $absPath = __DIR__ . "/../" . $relPath;

        if (file_exists($absPath))
        {
            return $relPath;
        }

        return "images/placeholder.jpg"; // <--- Das Bild kann jederzeit umgeändert werden!
    }

    /**
     * Leitet den Benutzer mit einer spezifischen Fehlermeldung auf die Fehlerseite weiter.
     *
     * @param string $message Anzuzeigende Fehlermeldung.
     */
    public static function triggerError($message = "Ein Fehler ist aufgetreten.")
    {
        header("Location: /Web-projekt/pages/error.php?msg=" . urlencode($message));
        exit;
    }
}
