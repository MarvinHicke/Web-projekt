<?php
class Helper
{
    public static function checkQueryId($id)
    {
        if (!isset($id) || !is_numeric($id))
        {
            header("Location: /Web-projekt/pages/error.php");
            exit;
        }
    }

    public static function getImagePath($filename, $subfolder, $size = 'medium')
    {

        $relPath = "images/" . $subfolder . "/" . $size . "/" . $filename . ".jpg";

        $absPath = __DIR__ . "/../" . $relPath;

        if (file_exists($absPath))
        {
            return $relPath;
        }

        return "images/placeholder.jpg";
    }

    public static function triggerError($message = "Ein Fehler ist aufgetreten.")
    {
        header("Location: /Web-projekt/pages/error.php?msg=" . urlencode($message));
        exit;
    }
}
