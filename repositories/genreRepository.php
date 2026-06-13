<?php

require_once __DIR__ . '/../model/genre.php';

/**
 * Repository für Datenbankabfragen rund um die Genres
 */
class genreRepository
{
    private $db;

    /**
     * Erstellt eine neue Instanz des genreRepository
     *
     * @param object $db Das Datenbank-Zugriffsobjekt (dbaccess)
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Holt alle Genres aus der Datenbank
     *
     * @return array Ein Array aus fertigen genre-Objekten
     */
    public function findAll()
    {
        $sql = "SELECT * FROM genres";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll();
        $genres = [];

        foreach ($rows as $row)
        {
            $genres[] = new genre($row);
        }

        return $genres;
    }

    /**
     * Sucht ein bestimmtes Genre anhand seiner ID
     *
     * @param int $id Die ID des Genres
     * @return genre Das gefundene genre-Objekt oder null falls nichts existiert
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM genres WHERE GenreID = :id";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? new genre($row) : null;
    }

    /**
     * Holt alle Genres sortiert nach Epoche und Name für die Übersicht
     *
     * @return array Ein Array aus fertigen genre-Objekten
     */
    public function getAllForBrowse()
    {
        $sql = "SELECT * FROM genres ORDER BY Era ASC, GenreName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Holt alle Genres die einem bestimmten Kunstwerk zugeordnet sind
     *
     * @param int $artworkId Die ID des Kunstwerks
     * @return array Ein Array aus fertigen genre-Objekten
     */
    public function getGenresForArtwork($artworkId)
    {
        $sql = "SELECT g.* FROM genres g, ArtworkGenres ag 
            WHERE g.GenreID = ag.GenreID AND ag.ArtWorkID = :artworkId
            ORDER BY g.GenreName ASC";

        $stmt = $this->db->preparedStatement($sql);
        $stmt->bindValue(':artworkId', (int)$artworkId, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $genres = [];
        foreach ($rows as $row)
        {
            $genres[] = new genre($row);
        }
        return $genres;
    }
}