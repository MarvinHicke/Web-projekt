<?php

class genre{
    private $genreid;
    private $genrename;
    private $era;
    private $description;
    private $link;

    public function __construct($data)
    {
        $this->genreid = $data['GenreID'];
        $this->genrename = $data['GenreName'];
        $this->era = $data['Era'];
        $this->description = $data['Description'];
        $this->link = $data['Link'];
    }

    // Getter

    public function getGenreID()
    {
        return $this->genreid;
    }

    public function getGenreName()
    {

        return $this->genrename;
    }

    public function getEra()
    {
        return $this->era;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getLink()
    {
        return $this->link;
    }
}

?>