<?php

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

    function getArtworkid ()
    {
        return $this->artworkid;
    }

    function getArtistid ()
    {
        return $this->artistid;
    }

    function getImagefilename ()
    {
        return $this->imagefilename;
    }

    function getTitle ()
    {
        return $this->title;
    }

    function getDescription ()
    {
        return $this->description;
    }

    function getExcerpt ()
    {
        return $this->excerpt;
    }

    function getArtworktype ()
    {
        return $this->artworktype;
    }

    function getYearofwork ()
    {
        return $this->yearofwork;
    }

    function getWidth ()
    {
        return $this->width;
    }

    function getHeight ()
    {
        return $this->height;
    }

    function getMedium ()
    {
        return $this->medium;
    }

    function getOriginalhome ()
    {
        return $this->originalhome;
    }

    function getGalleryid ()
    {
        return $this->galleryid;
    }

    function getArtworklink ()
    {
        return $this->artworklink;
    }

    function getGooglelink ()
    {
        return $this->googlelink;
    }
}