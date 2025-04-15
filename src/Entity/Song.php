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

    #[ORM\Column(nullable: true)]
    private ?bool $isListened = null;
    
    #[ORM\Column(nullable: true)]
    private ?int $noplpCount = 0;
    
    #[ORM\Column(nullable: true)]
    private ?int $normalPlayCount = 0;
    

    /**
     * @var Collection<int, Person>
     */
    #[ORM\ManyToMany(targetEntity: Person::class, inversedBy: 'songs')]
    private Collection $person;



    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $userSongKnowledge = null;
    
    public function getUserSongKnowledge(): ?string
    {
        return $this->userSongKnowledge;
    }
    
    public function setUserSongKnowledge(?string $userSongKnowledge): static
    {
        $validValues = ['unknown', 'little', 'well', 'by_heart'];  // Liste des valeurs valides
        if ($userSongKnowledge !== null && !in_array($userSongKnowledge, $validValues)) {
            throw new \InvalidArgumentException('Valeur invalide pour userSongKnowledge.');
        }
    
        $this->userSongKnowledge = $userSongKnowledge;
    
        return $this;
    }

    public function __construct()
    {
        $this->person = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->songReviews = new ArrayCollection();
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

    #[ORM\OneToMany(mappedBy: 'song', targetEntity: SongReview::class, cascade: ['persist', 'remove'])]
    private Collection $reviews;

    /**
     * @var Collection<int, SongReview>
     */
    #[ORM\OneToMany(targetEntity: SongReview::class, mappedBy: 'song')]
    private Collection $songReviews;

    public function addReview(): void
    {
        $this->reviews->add(new SongReview($this));
    }

    public function getReviewCount(): int
    {
        return $this->reviews->count();
    }

    public function getLastReviewDate(): ?\DateTimeInterface
    {
        return $this->reviews->last()?->getReviewedAt();
    }

    /**
     * @return Collection<int, SongReview>
     */
    public function getSongReviews(): Collection
    {
        return $this->songReviews;
    }

    public function addSongReview(SongReview $songReview): static
    {
        if (!$this->songReviews->contains($songReview)) {
            $this->songReviews->add($songReview);
            $songReview->setSong($this);
        }

        return $this;
    }

    public function removeSongReview(SongReview $songReview): static
    {
        if ($this->songReviews->removeElement($songReview)) {
            // set the owning side to null (unless already changed)
            if ($songReview->getSong() === $this) {
                $songReview->setSong(null);
            }
        }

        return $this;
    }

    public function isListened(): ?bool
    {
        return $this->isListened;
    }

    public function setIsListened(?bool $isListened): static
    {
        $this->isListened = $isListened;
        return $this;
    }

    public function setNormalPlayCount(int $count): static
    {
        $this->normalPlayCount = max(0, $count);
        return $this;
    }

    public function setNoplpCount(int $count): static
    {
        $this->noplpCount = max(0, $count);
        return $this;
    }

    public function incrementNormalPlayCount(int $amount = 1): static
    {
        $this->normalPlayCount += $amount;
        return $this;
    }

    public function incrementNoplpCount(int $amount = 1): static
    {
        $this->noplpCount += $amount;
        return $this;
    }

    public function getNormalPlayCount(): int
    {
        return $this->normalPlayCount;
    }

    public function getNoplpCount(): int
    {
        return $this->noplpCount;
    }


    public function isMastered(): bool
{
    return $this->userSongKnowledge === 'by_heart';
}

}
