<?php

require_once __DIR__ . "/../model/artist.php";

/**
 * Repository für Datenbankabfragen rund um den Künstler
 */
class artistRepository
{
    private $db;

    /**
     * Erstellt eine neue Instanz des artistRepository
     *
     * @param object $db Datenbank-Zugriffsobjekt (dbaccess)
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Holt alle Künstler aus der Datenbank und sortiert sie nach Nachname und Vorname
     *
     * @param string $sortOrder Die Sortierreihenfolge (ASC oder DESC). Standard ist ASC
     * @return array Ein Array aus fertigen artist-Objekten
     */
    public function findAll($sortOrder = 'ASC')
    {
        $sortOrder = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artists ORDER BY LastName " . $sortOrder . ", FirstName " . $sortOrder;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artists = [];

        foreach ($rows as $row)
        {
            $artists[] = new artist($row);
        }

        return $artists;
    }

    /**
     * Sucht ein bestimmten Künstler anhand seiner ID
     *
     * @param int $id Die eindeutige Datenbank-ID des Künstlers
     * @return artist Das gefundene artist-Objekt, falls die ID nicht existiert, sonst NULL
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM artists WHERE ArtistID = :id";

        $stmt = $this->db->preparedStatement($sql);

        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new Artist($row) : null;
    }

    /**
     * Holt die Künstler mit den meisten Bewertungen über all ihre Kunstwerke aus der Datenbank
     *
     * @param int $limit Die maximale Anzahl der zurückgegebenen Künstler (Standard: 3)
     * @return array Ein Array mit den Künstlerdaten und dem ReviewCount
     */
    public function getMostReviewedArtists($limit = 3)
    {
        $sql =
            "SELECT a.*,
               (SELECT COUNT(r.ReviewId)
                FROM reviews r, artworks aw
                WHERE r.ArtWorkId = aw.ArtWorkID AND aw.ArtistID = a.ArtistID) as ReviewCount
            FROM artists a
            ORDER BY ReviewCount DESC
            LIMIT :limit";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        $artists = [];
        while ($row = $stmt->fetch())
        {
            $artists[] = $row;
        }

        return $artists;
    }

    /**
     * Holt alle Künstler sortiert nach Nachname und Vorname
     *
     * @param string $direction Die Sortierreihenfolge (ASC oder DESC). Standard ist ASC
     * @return array Ein Array aus der Datenbankzeilen
     */
    public function getAllSorted($direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artists ORDER BY LastName " . $dir . ", FirstName " . $dir;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artists = [];

        foreach ($rows as $row)
        {
            $artists[] = new artist($row);
        }

        return $artists;
    }

    /**
     * Sucht künstler deren Nachname mit einem bestimmten Suchwort beginnt
     *
     * @param string $keyword Das Suchwort
     * @param string $direction Die Sortierreihenfolge ('ASC' oder 'DESC'). Standard ist 'ASC'
     * @return array Ein Array aus dem Treffer des Suchbegriffs
     */
    public function searchByLastName($keyword, $direction = 'ASC')
    {
        $searchString = $keyword . '%';
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT * FROM artists
                WHERE LastName LIKE :keyword
                ORDER BY LastName " . $dir;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['keyword' => $searchString]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artists = [];

        foreach ($rows as $row)
        {
            $artists[] = new artist($row);
        }

        return $artists;
    }
}

