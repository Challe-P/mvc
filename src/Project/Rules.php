<?php

namespace App\Project;

class Rules
{
    // One function for each possible hand, one checker that goes through them, returning when a hand is found
    // (Go from most to least points)

    private Row $row;
    /**
     * @var array<string>
     */
    private array $values = [];

    /**
     * @var array<string>
     */
    private array $suits = [];

    /**
     * @var array<string, array<int>>
     */
    private array $scoreDict = ["Royal flush" => [100, 30], "Straight flush" => [75, 30],
    "Four of a kind" => [50, 16], "Full house" => [25, 10], "Flush" => [20, 5], "Straight" => [15, 12], "Three of a kind" => [10, 6],
    "Two pairs" => [5, 3], "One pair" => [2, 1]];

    /**
     * @var array<string, int>
     */
    private array $translation = ['jack' => 11, 'queen' => 12, 'king' => 13, 'ace' => 14];
    /**
     * Returns an array with the american and english scores as index 0 and 1 respectively. 
     * @return array<int>
     */
    public function scoreRow(Row $row): array 
    {
        // Kanske som Camel Cards?  (Advent of code 7a) Smäll ihop handen och hitta unika. 
        // Kan ju lägga till färg där det är relevant?
        // Först få ut alla values
        $this->row = $row;
        if (!$this->row->isFilled()) {
            return [0, 0];
        }
        foreach ($this->row->getRow() as $card) {
            array_push($this->values, $card->getValue());
            array_push($this->suits, $card->getSuit());
        }
        $uniqueSuits = count(array_unique($this->suits));
        if ($uniqueSuits == 1) {
            return $this->flushChecker();
        }
        $uniqueValues = count(array_unique($this->values));
        switch ($uniqueValues)
        {
            case 5:
                return $this->straightChecker();
            case 4:
                return $this->scoreDict["One pair"];
            case 3:
                return $this->twoPairChecker();
            case 2:
                return $this->fullHouseChecker();
        }
        return [0, 0];
    }

    /** Checks a row for flush, straight flush and royal flush
     * @return array<int>
     */
    private function flushChecker(): array {
        if ($this->straightChecker() == [0, 0]) {
            return $this->scoreDict["Flush"];
        }


    }

    /** Checks a row for flush, straight flush and royal flush
     * @return array<int>
     */
    private function straightChecker(): array {
        // Gör om värden, sortera. Om ess - flippa ur
        list($straightTranslation, $aceExists) =  $this->straightTranslator();
        if ($aceExists) {
            $secondTranslation = $straightTranslation;
            $straightTranslation; // Hitta ässet och byt ut till 14, i den andra hitta ässet och byt ut till 1. 
        }
        sort($straightTranslation);

    }

    private function straightTranslator(): array 
    {
        if (in_array("ace", $this->values))
        {
            // gör något ballt.
        }
        $straightTranslation = [];
        foreach ($this->values as $value) {
            // Kanske göra ess-grejen här? Så blir det bara en genomkörning.
            if ($value == "ace") {
                // Men då måste efterföljande values också läggas till i secondtranslation
                $secondTranslation = $straightTranslation;
                $newValue = 1;
                array_push($secondTranslation, $newValue);
                $newValue = 14;
            }
            // Ternary operator to eliminate else.
            $newValue = isset($this->translation[$value]) ? $this->translation[$value] : (int) $value;
            array_push($straightTranslation, $newValue);
        }
        
        return 
    }

}
