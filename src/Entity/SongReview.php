<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SongReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private Song $song;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $reviewedAt;

    public function __construct()
    {
        $this->reviewedAt = new \DateTime();
    }

    // ... getters et setters ...

    /**
     * Get the value of song
     */ 
    public function getSong()
    {
        return $this->song;
    }

    /**
     * Set the value of song
     *
     * @return  self
     */ 
    public function setSong($song)
    {
        $this->song = $song;

        return $this;
    }
}