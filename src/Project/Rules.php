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
    private array $translation = ['ace' => 1, 'jack' => 11, 'queen' => 12, 'king' => 13];

    /**
     * @var array<int>
     */
    private array $translated = [];

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
            if ($card) {
                array_push($this->values, $card->getValue());
                array_push($this->suits, $card->getSuit());
            }
        }
        $uniqueSuits = count(array_unique($this->suits));
        if ($uniqueSuits == 1) {
            return $this->flushChecker();
        }
        $uniqueValues = count(array_unique($this->values));
        switch ($uniqueValues) {
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
    private function flushChecker(): array
    {
        $checkerResult = $this->straightChecker();
        if ($checkerResult == $this->scoreDict['Straight']) {
            // If the sum of the cards are 60, it's a royal straight.
            if (array_sum($this->translated) == 60) {
                return $this->scoreDict["Royal flush"];
            }
            return $this->scoreDict['Straight flush'];
        }
        return $this->scoreDict["Flush"];
    }

    /** Checks a row for a straight or a royal straight
     * @return array<int>
     */
    private function straightChecker(): array
    {
        // Gör om värden, sortera. Om ess - flippa ur
        $straight = false;
        $straightTranslation = [];
        $secondTranslation = [];
        $aceExists = false;
        foreach ($this->values as $value) {
            if ($value == "ace") {
                $aceExists = true;
            }
            // Ternary operator to eliminate else.
            $newValue = isset($this->translation[$value]) ? $this->translation[$value] : (int) $value;
            array_push($straightTranslation, $newValue);
        }
        sort($straightTranslation);
        if ($aceExists) {
            $secondTranslation = $straightTranslation;
            array_shift($secondTranslation);
            array_push($secondTranslation, 14);
        }
        $ladderValue = $straightTranslation[0];
        $straight = true;
        for ($i = 0; $i < 5; $i++) {
            if ($ladderValue != $straightTranslation[$i]) {
                $straight = false;
                if (!$secondTranslation) {
                    return [0, 0];
                }
            }
            $ladderValue++;
        }
        if ($secondTranslation != []) {
            $ladderValue = $secondTranslation[0];
            for ($i = 0; $i < 5; $i++) {
                if ($ladderValue != $secondTranslation[$i] && !$straight) {
                    return [0, 0];
                }
                array_push($this->translated, (int) $secondTranslation[$i]);
                $ladderValue++;
            }
        }
        return $this->scoreDict['Straight'];

    }

    /**
     * This checks for two pairs or three of a kind.
     * @return array<int>
     */
    private function twoPairChecker(): array
    {
        $countArray = array_count_values($this->values);
        // if any frequency is three - three of a kind else two pairs.
        foreach (array_values($countArray) as $value) {
            if ($value == 3) {
                return $this->scoreDict['Three of a kind'];
            }
        }
        return $this->scoreDict['Two pair'];
    }

    /**
     * Checks if row is full house or four of a kind
     * @return array<int>
     */
    private function fullHouseChecker(): array
    {
        $countArray = array_count_values($this->values);
        // if any frequency is three - three of a kind else two pairs.
        foreach (array_values($countArray) as $value) {
            if ($value == 3) {
                return $this->scoreDict['Full house'];
            }
        }
        return $this->scoreDict['Four of a kind'];
    }
}
