<?php

class gallery{
    private $galleryid;
    private $galleryname;
    private $gallerynativename;
    private $gallerycountry;
    private $latitude;
    private $longitude;
    private $gallerywebsite;

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

    public function getGalleryID()
    {
    return $this->galleryid;
    }

    public function getGalleryName()
    {
        return $this->galleryname;
    }

    public function getGalleryNativeName()
    {
        return $this->gallerynativename;
    }

    public function getGalleryCountry()
    {
        return $this->gallerycountry;
    }

    public function getGalleryWebsite()
    {
        return $this->gallerywebsite;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }
}