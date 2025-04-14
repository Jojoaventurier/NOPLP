<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Form\Enum\UserSongKnowledgeEnum;

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

}
