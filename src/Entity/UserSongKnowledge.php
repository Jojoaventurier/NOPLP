<?php

namespace App\Entity;

use App\Repository\UserSongKnowledgeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserSongKnowledgeRepository::class)]
class UserSongKnowledge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $knowledgeLevel = null;

    /**
     * @var Collection<int, Song>
     */
    #[ORM\OneToMany(targetEntity: Song::class, mappedBy: 'userSongKnowledge')]
    private Collection $songs;

    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKnowledgeLevel(): ?int
    {
        return $this->knowledgeLevel;
    }

    public function setKnowledgeLevel(?int $knowledgeLevel): static
    {
        $this->knowledgeLevel = $knowledgeLevel;

        return $this;
    }

    /**
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setUserSongKnowledge($this);
        }

        return $this;
    }

    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            // set the owning side to null (unless already changed)
            if ($song->getUserSongKnowledge() === $this) {
                $song->setUserSongKnowledge(null);
            }
        }

        return $this;
    }
}
