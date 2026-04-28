<?php

class subject{
    private $subjectid;
    private $subjectname;


    public function __construct($data)
    {
        $this->subjectid = $data['SubjectId'];
        $this->subjectname = $data['SubjectName'];
    }

    // Getter

    public function getSubjectid()
    {
    return $this->subjectid;
    }

    public function getSubjectname()
    {
        return $this->subjectname;
    }
}

