<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $bet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $finished = null;

    #[ORM\Column]
    private ?int $american_score = null;

    #[ORM\Column]
    private ?int $british_score = null;

    #[ORM\Column]
    private ?int $winnings = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Player $player_id = null;

    #[ORM\Column(length: 255)]
    private ?string $deck = null;

    #[ORM\Column(length: 255)]
    private ?string $placement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $saved_date = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBet(): ?int
    {
        return $this->bet;
    }

    public function setBet(int $bet): static
    {
        $this->bet = $bet;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getFinished(): ?\DateTimeInterface
    {
        return $this->finished;
    }

    public function setFinished(\DateTimeInterface $finished): static
    {
        $this->finished = $finished;

        return $this;
    }

    public function getAmericanScore(): ?int
    {
        return $this->american_score;
    }

    public function setAmericanScore(int $american_score): static
    {
        $this->american_score = $american_score;

        return $this;
    }

    public function getBritishScore(): ?int
    {
        return $this->british_score;
    }

    public function setBritishScore(int $british_score): static
    {
        $this->british_score = $british_score;

        return $this;
    }

    public function getWinnings(): ?int
    {
        return $this->winnings;
    }

    public function setWinnings(int $winnings): static
    {
        $this->winnings = $winnings;

        return $this;
    }

    public function getPlayerId(): ?Player
    {
        return $this->player_id;
    }

    public function setPlayerId(?Player $player_id): static
    {
        $this->player_id = $player_id;

        return $this;
    }

    public function getDeck(): ?string
    {
        return $this->deck;
    }

    public function setDeck(string $deck): static
    {
        $this->deck = $deck;

        return $this;
    }

    public function getPlacement(): ?string
    {
        return $this->placement;
    }

    public function setPlacement(string $placement): static
    {
        $this->placement = $placement;

        return $this;
    }

    public function getSavedDate(): ?\DateTimeInterface
    {
        return $this->saved_date;
    }

    public function setSavedDate(\DateTimeInterface $saved_date): static
    {
        $this->saved_date = $saved_date;

        return $this;
    }
}
