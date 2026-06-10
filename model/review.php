<?php

/**
 * Repräsentiert eine Bewertung (Review) eines Kunstwerks aus der Datenbank
 */
class review
{
    private $id;
    private $artworkId;
    private $rating;
    private $comment;
    private $reviewDate;
    private $customerId;
    private $customerName;

    /**
     * Erstellt ein neues Review-Objekt anhand eines Datenbank-Datensatzes
     *
     * @param array $row Ein Array mit den Datenbankwerten
     */
    public function __construct($row)
    {
        $this->id = $row['ReviewId'] ?? null;
        $this->artworkId = $row['ArtWorkId'] ?? null;
        $this->customerId =$row['CustomerId'] ?? null;
        $this->rating = $row['Rating'] ?? 0;
        $this->comment = $row['Comment'] ?? '';
        $this->reviewDate = $row['ReviewDate'] ?? '';

        $firstName = trim((string) ($row['FirstName'] ?? ''));
        $lastName = trim((string) ($row['LastName'] ?? ''));
        $userName = (string) ($row['UserName'] ?? '');

        $fullName = trim($firstName . ' ' . $lastName);

        if ($fullName !== '') {
            $this->customerName = $fullName;
        } elseif ($userName !== '') {
            $this->customerName = $userName;
        } else {
            $this->customerName = 'Unbekannter Nutzer';
        }
    }

    // Getter

    /**
     * Gibt das rohe Datum der Bewertung zurück
     *
     * @return string Das Bewertungsdatum
     */
    public function getReviewDate()
    {
        return $this->reviewDate;
    }

    /**
     * Gibt die ID des bewerteten Kunstwerks zurück
     *
     * @return int Die Kunstwerk-ID
     */
    public function getArtworkId()
    {
        return $this->artworkId;
    }

    /**
     * Gibt die ID des Kunden zurück (die Person die eine Bewertung abgegeben hat)
     *
     * @return int Die Kunden-ID
     */
    function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * Gibt die vergebene Bewertung zurück
     *
     * @return int Die Bewertung (1 - 5)
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Gibt den Kommentar zurück
     *
     * @return string Der Kommentar
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Gibt die eindeutige ID der Bewertung zurück
     *
     * @return int Die Review-ID
     */
    public function getReviewId()
    {
        return $this->id;
    }

    /**
     * Gibt das Datum der Bewertung im formatierten deutschen Format zurück
     *
     * @return string Das formatierte Datum
     */
    public function getReviewDateFormatted()
    {
        return date('d.m.Y', strtotime($this->reviewDate));
    }

    /**
     * Gibt den Anzeigenamen des Kunden zurück, der die Bewertung geschrieben hat
     *
     * @return string Der Kundenname oder Benutzername
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }
}