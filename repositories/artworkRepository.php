<?php

require_once __DIR__ . "/../model/artwork.php";

/**
 * Repository für Datenbankabfragen rund um die Kunstwerke
 */
class artworkRepository
{

    private $db;

    /**
     * Erstellt eine neue Instanz des artworkRepository
     *
     * @param object $db Das Datenbank-Zugriffsobjekt (dbaccess)
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Holt alle Kunstwerke aus der Datenbank, inklusive der verknüpften Künstlerdaten
     *
     * @param string $sortOrder Die Sortierreihenfolge ('ASC' oder 'DESC'). Standard ist 'ASC'
     * @return array Ein Array aus fertigen artwork-Objekten
     */
    public function findAll($sortOrder = 'ASC')
    {
        $sortOrder = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $sql = "SELECT a.*, art.FirstName, art.LastName 
        FROM artworks a, artists art
        WHERE a.ArtistID = art.ArtistID
        ORDER BY a.Title " . $sortOrder;

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    /**
     * Sucht ein bestimmtes Kunstwerk anhand seiner ID
     *
     * @param int $id Die eindeutige Datenbank-ID des Kunstwerks
     * @return artwork Das gefundene artwork-Objekt oder null, falls die ID nicht existiert
     */
    public function getById($id)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName
            FROM artworks a
            JOIN artists art ON a.ArtistID = art.ArtistID
            WHERE a.ArtWorkID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute
        ([
            'id' => (int) $id
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new artwork($row) : null;
    }

    /**
     * Sucht ein bestimmtes Kunstwerk anhand seiner ID
     *
     * @param int $id Die eindeutige Datenbank-ID des Kunstwerks
     * @return artwork Das gefundene artwork-Objekt oder null, falls die ID nicht existiert
     */
    public function getByIdOrigin($id)
    {
        $sql = "SELECT * FROM artworks WHERE ArtWorkID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new artwork($row) : null;
    }

    /**
     * Holt die am besten bewerteten Kunstwerke mit mind. 3 Bewertungen
     *
     * @param int $limit Die maximale Anzahl der zurückgegebenen Kunstwerke (Standard ist 3)
     * @return array Ein Array aus Arrays mit den Kunstwerkdaten und der Durchschnittsbewertung (AvgRating).
     */
    public function getTopArtworks($limit = 3)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName,
                   (SELECT IF(COUNT(r.ReviewId) >= 3, AVG(r.Rating), NULL) 
                    FROM reviews r 
                    WHERE r.ArtWorkId = a.ArtWorkID) as AvgRating
            FROM artworks a
            /* D: Künstlername einbinden */
            JOIN artists art ON a.ArtistID = art.ArtistID 
            /* D: Kunstwerke ohne Bild ausschließen */
            WHERE a.ImageFileName IS NOT NULL AND a.ImageFileName != ''
            ORDER BY AvgRating DESC
            LIMIT :limit";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        $artworks = [];
        while ($row = $stmt->fetch())
        {
            $artworks[] = $row;
        }

        return $artworks;
    }

    /**
     * Holt alle Kunstwerke und sortiert sie nach einem bestimmten Kriterium
     *
     * @param string $sortBy Das Sortierkriterium ('title', 'year' oder 'artist'). Standard ist 'title'
     * @param string $direction Die Sortierrichtung ('ASC' oder 'DESC'). Standard ist 'ASC'
     * @return array Ein Array aus fertigen artwork-Objekten.
     */
    public function getAllSorted($sortBy = 'title', $direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        switch (strtolower($sortBy)) {
            case 'year':
                $orderClause = "a.YearOfWork $dir";
                break;
            case 'artist':
                $orderClause = "art.LastName $dir, art.FirstName $dir";
                break;
            case 'title':
            default:
                $orderClause = "a.Title $dir";
                break;
        }

        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a
            JOIN artists art ON a.ArtistID = art.ArtistID
            ORDER BY $orderClause";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    /**
     * Holt alle Kunstwerke eines bestimmten Künstlers
     *
     * @param int $artistId Die eindeutige ID des Künstlers
     * @return array Ein Array aus fertigen artwork-Objekten
     */
    public function getForArtist($artistId)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName 
        FROM artworks a, artists art
        WHERE a.ArtistID = art.ArtistID AND a.ArtistID = :artistId
        ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':artistId', (int)$artistId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    /**
     * Holt alle Kunstwerke, die einem bestimmten Genre zugeordnet sind
     *
     * @param int $genreId Die eindeutige ID des Genres
     * @return array Ein Array aus fertigen artwork-Objekten
     */
    public function getForGenre($genreId)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a, ArtworkGenres ag, artists art
            WHERE a.ArtWorkID = ag.ArtWorkID 
              AND a.ArtistID = art.ArtistID 
              AND ag.GenreID = :genreId
            ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':genreId', (int)$genreId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    /**
     * Holt alle Kunstwerke, die einem bestimmten Thema zugeordnet sind
     *
     * @param int $subjectId Die eindeutige ID des Themas
     * @return array Ein Array aus fertigen artwork-Objekten
     */
    public function getForSubject($subjectId)
    {
        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a, ArtworkSubjects xas, artists art
            WHERE a.ArtWorkID = xas.ArtWorkID 
              AND a.ArtistID = art.ArtistID 
              AND xas.SubjectID = :subjectId
            ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':subjectId', (int)$subjectId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    /**
     * Sucht Kunstwerke, deren Titel mit einem bestimmten Suchwort beginnt
     *
     * @param string $keyword Das Suchwort
     * @return array Ein Array aus fertigen artwork-Objekten
     */
    public function searchByTitle($keyword)
    {
        $searchString = $keyword . '%';

        $sql = "SELECT a.*, art.FirstName, art.LastName 
            FROM artworks a, artists art 
            WHERE a.ArtistID = art.ArtistID AND a.Title LIKE :keyword 
            ORDER BY a.Title ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['keyword' => $searchString]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }

    /**
     * Erweiterte Suche für Kunstwerke mit dynamischen Suchkriterien
     */
    public function advancedSearch($title = '', $yearMin = null, $yearMax = null, $genreId = null, $sortBy = 'title', $direction = 'ASC')
    {
        $dir = (strtoupper($direction) === 'DESC') ? 'DESC' : 'ASC';

        switch (strtolower($sortBy))
        {
            case 'year':
                $orderClause = "a.YearOfWork $dir";
                break;
            case 'artist':
                $orderClause = "art.LastName $dir, art.FirstName $dir";
                break;
            case 'title':
            default:
                $orderClause = "a.Title $dir";
                break;
        }

        $fromClause = "artworks a, artists art";
        $whereClause = "a.ArtistID = art.ArtistID";
        $params = [];

        if (!empty($genreId))
        {
            $fromClause .= ", ArtworkGenres ag";
            $whereClause .= " AND a.ArtWorkID = ag.ArtWorkID AND ag.GenreID = :genreId";
            $params['genreId'] = (int)$genreId;
        }

        if (!empty($title))
        {
            $whereClause .= " AND a.Title LIKE :title";
            $params['title'] = '%' . $title . '%';
        }

        if (!empty($yearMin))
        {
            $whereClause .= " AND a.YearOfWork >= :yearMin";
            $params['yearMin'] = (int)$yearMin;
        }

        if (!empty($yearMax))
        {
            $whereClause .= " AND a.YearOfWork <= :yearMax";
            $params['yearMax'] = (int)$yearMax;
        }

        $sql = "SELECT a.*, art.FirstName, art.LastName 
                FROM $fromClause 
                WHERE $whereClause 
                ORDER BY $orderClause";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $artworks = [];

        foreach ($rows as $row)
        {
            $artworks[] = new artwork($row);
        }

        return $artworks;
    }
}

