<?php

namespace App\Game;

use App\Game\CardHand;

class Player
{
    private string $name;
    private CardHand $hand;
    private int $score;

    public function __construct(string $name, CardHand $cardHand, int $score = 0)
    {
        $this->name = $name;
        $this->hand = $cardHand;
        $this->score = $score;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getHand(): CardHand
    {
        return $this->hand;
    }

    public function setHand(CardHand $cardHand): void
    {
        $this->hand = $cardHand;
    }
}
