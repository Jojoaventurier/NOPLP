<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lyrics = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDuo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDownloaded = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hasLyrics = null;

    /**
     * @var Collection<int, Person>
     */
    #[ORM\ManyToMany(targetEntity: Person::class, inversedBy: 'songs')]
    private Collection $person;

    #[ORM\ManyToOne(inversedBy: 'songs')]
    private ?UserSongKnowledge $userSongKnowledge = null;

    public function __construct()
    {
        $this->person = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLyrics(): ?string
    {
        return $this->lyrics;
    }

    public function setLyrics(?string $lyrics): static
    {
        $this->lyrics = $lyrics;

        return $this;
    }

    public function isDuo(): ?bool
    {
        return $this->isDuo;
    }

    public function setIsDuo(?bool $isDuo): static
    {
        $this->isDuo = $isDuo;

        return $this;
    }

    public function isDownloaded(): ?bool
    {
        return $this->isDownloaded;
    }

    public function setIsDownloaded(?bool $isDownloaded): static
    {
        $this->isDownloaded = $isDownloaded;

        return $this;
    }

    public function hasLyrics(): ?bool
    {
        return $this->hasLyrics;
    }

    public function setHasLyrics(?bool $hasLyrics): static
    {
        $this->hasLyrics = $hasLyrics;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPerson(): Collection
    {
        return $this->person;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->person->contains($person)) {
            $this->person->add($person);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        $this->person->removeElement($person);

        return $this;
    }

    public function getUserSongKnowledge(): ?UserSongKnowledge
    {
        return $this->userSongKnowledge;
    }

    public function setUserSongKnowledge(?UserSongKnowledge $userSongKnowledge): static
    {
        $this->userSongKnowledge = $userSongKnowledge;

        return $this;
    }
}
