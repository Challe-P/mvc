<?php

// Ska innehålla en matta, och prata med controllern. Ingen annan av klasserna ska prata med controllern.

namespace App\Project;

use App\Project\Mat;
use App\Game\DeckOfCards;
use App\Game\Card;
use App\Project\Exceptions\PositionFilledException;

class PokerLogic
{
    public DeckOfCards $deck;

    public Mat $mat;

    public Rules $rules;

    public Card $nextCard;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->mat = new Mat();
        $this->rules = new Rules();
        $this->nextCard = $this->deck->peek();
    }

    public function autofill(): Mat
    {
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                try {
                $this->mat->setCard($i, $j, $this->deck->drawCard());
                }
                catch (PositionFilledException)
                {
                    // Go to next position.
                }
            }
        }
        return $this->mat;
    }

    /**
     * @return array<int>
     */
    public function checkScore(): array
    {
        $americanScore = 0;
        $englishScore = 0;
        foreach ($this->mat->getHorizontalRows() as $row) {
            $score = $this->rules->scoreRow($row);
            $row->setScore($score);
            $americanScore += $score[0];
            $englishScore += $score[1];
        }

        foreach ($this->mat->getVerticalRows() as $row) {
            $score = $this->rules->scoreRow($row);
            $row->setScore($score);
            $americanScore += $score[0];
            $englishScore += $score[1];
        }
        $this->mat->setScore([$americanScore, $englishScore]);
        return [$americanScore, $englishScore];
    }

    public function setCard(int $horizontalPosition, int $verticalPosition, Card $card = null): void
    {
        if ($card) {
            $this->mat->setCard($horizontalPosition, $verticalPosition, $card);
        }
        $this->mat->setCard($horizontalPosition, $verticalPosition, $this->deck->drawCard());
        $this->nextCard = $this->deck->peek();
    }
    //public function load
    //public function save
}
