<?php

namespace App\Entity;

use App\Repository\TvShowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TvShowRepository::class)]
class TvShow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $airDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAirDate(): ?\DateTimeInterface
    {
        return $this->airDate;
    }

    public function setAirDate(?\DateTimeInterface $airDate): static
    {
        $this->airDate = $airDate;

        return $this;
    }
}
