<?php


/**
 * Repräsentiert ein Artwork mit seinen Details aus der Datenbank
 */
class artwork
{
    private $artworkid;
    private $artistid;
    private $imagefilename;
    private $title;
    private $description;
    private $excerpt;
    private $artworktype;
    private $yearofwork;
    private $width;
    private $height;
    private $medium;
    private $originalhome;
    private $galleryid;
    private $artworklink;
    private $googlelink;

    /**
     * Erstellt ein neues Artwork-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->artworkid = $data['ArtWorkID'];
        $this->artistid = $data['ArtistID'];
        $this->imagefilename = $data['ImageFileName'];
        $this->title = $data['Title'];
        $this->description = $data['Description'];
        $this->excerpt = $data['Excerpt'];
        $this->artworktype = $data['ArtWorkType'];
        $this->yearofwork = $data['YearOfWork'];
        $this->width = $data['Width'];
        $this->height = $data['Height'];
        $this->medium = $data['Medium'];
        $this->originalhome = $data['OriginalHome'];
        $this->galleryid = $data['GalleryID'];
        $this->artworklink = $data['ArtWorkLink'];
        $this->googlelink = $data['GoogleLink'];
    }

    // Getter

    /**
     * Gibt die eindeutige ID des Kunstwerks zurück
     *
     * @return int Die Kunstwerk-ID
     */
    function getArtworkid ()
    {
        return $this->artworkid;
    }

    /**
     * Gibt die ID des zugehörigen Künstlers zurück
     *
     * @return int Die Künstler-ID
     */
    function getArtistid ()
    {
        return $this->artistid;
    }

    /**
     * Gibt den Dateinamen des zugehörigen Bildes zurück
     *
     * @return string Der Bilddateiname
     */
    function getImagefilename ()
    {
        return $this->imagefilename;
    }

    /**
     * Gibt den Titel des Kunstwerks zurück
     *
     * @return string Der Titel
     */
    function getTitle ()
    {
        return $this->title;
    }

    /**
     * Gibt die  Beschreibung des Kunstwerks zurück
     *
     * @return string Die Beschreibung
     */
    function getDescription ()
    {
        return $this->description;
    }

    /**
     * Gibt einen kurzen Auszug der Beschreibung zurück
     *
     * @return string Der Auszug
     */
    function getExcerpt ()
    {
        return $this->excerpt;
    }

    /**
     * Gibt die Art des Kunstwerks zurück
     *
     * @return int Die Kunstwerk-Art
     */
    function getArtworktype ()
    {
        return $this->artworktype;
    }

    /**
     * Gibt das Jahr der Entstehung des Kunstwerks zurück
     *
     * @return int Das Jahr der Entstehung
     */
    function getYearofwork ()
    {
        return $this->yearofwork;
    }

    /**
     * Gibt die Breite des Kunstwerks zurück
     *
     * @return int Die Breite
     */
    function getWidth ()
    {
        return $this->width;
    }

    /**
     * Gibt die Höhe des Kunstwerks zurück
     *
     * @return int Die Höhe.
     */
    function getHeight ()
    {
        return $this->height;
    }

    /**
     * Gibt das verwendete Medium zurück
     *
     * @return string Das Medium
     */
    function getMedium ()
    {
        return $this->medium;
    }

    /**
     * Gibt den ursprünglichen Standort zurück
     *
     * @return string Der ursprüngliche Standort.
     */
    function getOriginalhome ()
    {
        return $this->originalhome;
    }

    /**
     * Gibt die ID der Galerie zurück, in der das Kunstwerk aktuell ausgestellt ist
     *
     * @return int Die Galerie-ID
     */
    function getGalleryid ()
    {
        return $this->galleryid;
    }

    /**
     * Gibt den Link zu weiteren Informationen über das Kunstwerk zurück
     *
     * @return string Der externe Link
     */
    function getArtworklink ()
    {
        return $this->artworklink;
    }

    /**
     * Gibt den Google-Suchlink für das Kunstwerk zurück
     *
     * @return string Der Google-Link
     */
    function getGooglelink ()
    {
        return $this->googlelink;
    }
}