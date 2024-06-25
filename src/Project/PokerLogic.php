<?php

namespace App\Project;

use App\Project\Mat;
use App\Game\DeckOfCards;
use App\Game\Card;
use App\Project\Exceptions\PositionFilledException;

/**
 * A class used as a link between the controllers and all the classes used for the game
 * Contains a mat, rules, a deck of cards and what bet the player chose.
 */
class PokerLogic
{
    public DeckOfCards $deck;

    public Mat $mat;

    public Rules $rules;

    public ?Card $nextCard;

    public bool $finished;

    public ?int $bet;

    /**
     * Construct the logic, if loading a previous game send in the string for the deck and mat.
     */
    public function __construct(string $deckString = "", string $matString = "", int $bet = 10)
    {
        $this->deck = new DeckOfCards($deckString);
        if ($deckString == "") {
            $this->deck->shuffle();
        }
        $this->mat = new Mat($matString);
        $this->rules = new Rules();
        $this->nextCard = $this->deck->peek();
        $this->finished = false;
        $this->bet = $bet;
    }

    /**
     * Autofills the mat
     */
    public function autofill(): void
    {
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                try {
                    $this->setCard($i, $j);
                } catch (PositionFilledException $e) {
                    // Do nothing.
                }
            }
        }
        $this->checkScore();
    }

    /**
     * Checks the score of the mat
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

        $this->checkFinished();
        return [$americanScore, $englishScore];
    }

    /**
     * Sets a card on a position
     */
    public function setCard(int $horizontalPosition, int $verticalPosition, Card $card = null): void
    {
        try {
            if ($card) {
                $this->mat->setCard($horizontalPosition, $verticalPosition, $card);
                return;
            }
            $this->mat->setCard($horizontalPosition, $verticalPosition, $this->deck->drawCard());
            $this->nextCard = $this->deck->peek();
        } catch (PositionFilledException $e) {
            throw $e;
        }
    }

    /**
     * Checks if every horizontal row is filled, changes finished if it is.
     */
    private function checkFinished(): void
    {
        foreach ($this->mat->getHorizontalRows() as $row) {
            if (!$row->isFilled()) {
                return;
            }
        }
        $this->finished = true;
        $this->nextCard = null;
        return;
    }
}
