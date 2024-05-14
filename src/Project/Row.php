<?php

// ärva från cardhand?
// innehåller fem kort.

namespace App\Project;

use App\Game\Card;
use App\Project\Exceptions\PositionFilledException;

class Row
{
    /**
     * @var array<?Card> An array holding 0-5 cards and/or 0-5 null values.
     */
    private array $cards = [null, null, null, null, null];
    /**
     * @var array<int>
     */
    private array $score = [0, 0];

    public function setCard(int $position, Card $card): void
    {
        if ($this->cards[$position]) {
            throw new PositionFilledException();
        }
        array_splice($this->cards, $position, 1, array($card));
    }

    /**
     * @return array<?Card>
     */
    public function getRow(): array
    {
        return $this->cards;
    }

    public function isFilled(): bool
    {
        return !in_array(null, $this->cards);
    }

    public function __toString(): String
    {
        $outputArray = [];
        foreach ($this->cards as $element) {
            // Change here if you want to print something other than null
            $element = $element ?? "null";
            array_push($outputArray, $element);
        }
        $output = implode(", ", $outputArray);
        return $output;
    }

    /**
     * @param array<int> $score
     */
    public function setScore(array $score): void
    {
        $this->score = $score;
    }

    /**
     * @return array<int>
     */
    public function getScore(): array
    {
        return $this->score;
    }
}
