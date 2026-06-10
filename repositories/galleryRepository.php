<?php

require_once __DIR__ . "/../model/gallery.php";

/**
 * Repository für Datenbankabfragen rund um die Galerien
 */
class galleryRepository
{
    private $db;

    /**
     * Erstellt eine neue Instanz des galleryRepository
     *
     * @param object $db Das Datenbank-Zugriffsobjekt (dbaccess)
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Holt alle Galerien aus der Datenbank und gibt sie als Objekte zurück
     *
     * @return array Ein Array aus fertigen gallery-Objekten
     */
    public function findAll()
    {
        $sql = "SELECT * FROM galleries";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $galleries= [];

        foreach ($rows as $row)
        {
            $galleries[] = new gallery($row);
        }

        return $galleries;
    }

    /**
     * Sucht eine bestimmte Galerie anhand ihrer ID
     *
     * @param int $id Die eindeutige Datenbank-ID der Galerie
     * @return gallery Das gefundene gallery-Objekt oder null falls nichts existiert
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM galleries WHERE GalleryID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new gallery($row) : null;
    }

}

