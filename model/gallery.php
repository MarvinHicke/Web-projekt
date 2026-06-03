<?php

/**
 * Repräsentiert eine Galerie (Gallery) mit ihren Details aus der Datenbank
 */
class gallery
{
    private $galleryid;
    private $galleryname;
    private $gallerynativename;
    private $gallerycountry;
    private $latitude;
    private $longitude;
    private $gallerywebsite;

    /**
     * Erstellt ein neues Gallery-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $data Ein Array mit den Datenbankwerten
     */
    public function __construct($data)
    {
        $this->galleryid = $data['GalleryID'];
        $this->galleryname = $data['GalleryName'];
        $this->gallerynativename = $data['GalleryNativeName'];
        $this->gallerycountry = $data['GalleryCountry'];
        $this->latitude = $data['Latitude'];
        $this->longitude = $data['Longitude'];
        $this->gallerywebsite = $data['GalleryWebSite'];
    }

    // Getter

    /**
     * Gibt die ID der Galerie zurück
     *
     * @return int Die Galerie-ID
     */
    public function getGalleryID()
    {
        return $this->galleryid;
    }

    /**
     * Gibt den Namen der Galerie zurück
     *
     * @return string Der Galeriename
     */
    public function getGalleryName()
    {
        return $this->galleryname;
    }

    /**
     * Gibt den ursprünglichen Namen der Galerie zurück
     *
     * @return string Der einheimische Name
     */
    public function getGalleryNativeName()
    {
        return $this->gallerynativename;
    }

    /**
     * Gibt das Land der Galerie zurück
     *
     * @return string Das Land
     */
    public function getGalleryCountry()
    {
        return $this->gallerycountry;
    }

    /**
     * Gibt die Website der Galerie zurück
     *
     * @return string Die Website-URL
     */
    public function getGalleryWebsite()
    {
        return $this->gallerywebsite;
    }

    /**
     * Gibt den Breitengrad der Galerie zurück
     *
     * @return float Der Breitengrad
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Gibt den Längengrad der Galerie zurück
     *
     * @return float Der Längengrad
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}