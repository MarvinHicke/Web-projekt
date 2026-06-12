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
     * Holt alle Kunstwerke und sortiert sie nach einem bestimmten Kriterium
     *
     * @param string $sortBy Das Sortierkriterium ('title', 'year' oder 'artist'). Standard ist 'title'
     * @param string $direction Die Sortierrichtung ('ASC' oder 'DESC'). Standard ist 'ASC'
     * @return array Ein Array aus fertigen artwork-Objekten.
     */
    public function getAllSorted($sortBy = 'FirstName', $direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        switch (strtolower($sortBy)) {
            case 'artistFirstName':
                $orderClause = "art.FirstName $dir, art.LastName $dir";
                break;
            case 'artistLastName':
                $orderClause = "art.LastName $dir, art.FirstName $dir";
                break;
            default:
                $orderClause = "art.FirstName $dir";
                break;
        }

        $sql = "SELECT art.*, art.FirstName, art.LastName 
            FROM artists art
            ORDER BY $orderClause";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

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

    /**
     * Holt eine Liste aller eindeutigen Nationalitäten für das Dropdown-Menü der erweiterten Suche.
     *
     * @return array Ein Array von Strings mit den Nationalitäten.
     */
    public function getNationalities()
    {
        $sql = "SELECT DISTINCT Nationality FROM artists 
                WHERE Nationality IS NOT NULL AND Nationality != '' 
                ORDER BY Nationality ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $nationalities = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $nationalities[] = $row['Nationality'];
        }
        return $nationalities;
    }

    /**
     * Erweiterte Suche für Künstler mit dynamischen Suche
     *
     * @param string $name Suchbegriff für Vor- oder Nachname
     * @param int $yearMin Frühestes Lebensjahr
     * @param int $yearMax Spätestes Lebensjahr
     * @param string $nationality Die Nationalität
     * @param string $direction Sortierrichtung
     * @return array Ein Array aus artist-Objekten, die den Kriterien entsprechen
     */
    public function advancedSearch($name = '', $yearMin = null, $yearMax = null, $nationality = '', $direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';
        $sql = "SELECT * FROM artists WHERE 1=1";
        $params = [];

        if (!empty($name))
        {
            $sql .= " AND (FirstName LIKE :name1 OR LastName LIKE :name2)";
            $params['name1'] = '%' . $name . '%';
            $params['name2'] = '%' . $name . '%';
        }
        if (!empty($nationality))
        {
            $sql .= " AND Nationality = :nationality";
            $params['nationality'] = $nationality;
        }

        if (!empty($yearMin))
        {
            $sql .= " AND (YearOfDeath >= :yearMin OR YearOfDeath IS NULL)";
            $params['yearMin'] = (int)$yearMin;
        }
        if (!empty($yearMax))
        {
            $sql .= " AND YearOfBirth <= :yearMax";
            $params['yearMax'] = (int)$yearMax;
        }

        $sql .= " ORDER BY LastName $dir, FirstName $dir";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artists = [];
        foreach ($rows as $row)
        {
            $artists[] = new artist($row);
        }
        return $artists;
    }
}

