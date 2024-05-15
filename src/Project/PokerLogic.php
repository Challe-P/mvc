<?php

// Ska innehålla en matta, och prata med controllern. Ingen annan av klasserna ska prata med controllern.

namespace App\Project;

use App\Project\Mat;
use App\Game\DeckOfCards;

class PokerLogic
{
    private DeckOfCards $deck;

    public Mat $mat;

    public Rules $rules;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->mat = new Mat();
        $this->rules = new Rules();
    }
    public function autofill(): Mat
    {
        $this->deck->shuffle();
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $this->mat->setCard($i, $j, $this->deck->drawCard());
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
        foreach ($this->mat->getHorizontalRows() as $row)
        {
            $score = $this->rules->scoreRow($row);
            $row->setScore($score);
            $americanScore += $score[0];
            $englishScore += $score[1];
        }
        
        foreach ($this->mat->getVerticalRows() as $row)
        {
            $score = $this->rules->scoreRow($row);
            $row->setScore($score);
            $americanScore += $score[0];
            $englishScore += $score[1];
        }
        return [$americanScore, $englishScore];
    }
    //public function load
    //public function save
}
