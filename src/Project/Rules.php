<?php

namespace App\Project;

/**
 * Class for checking if a row is valid and what score the row has.
 */
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
        $this->extractor();
        $uniqueSuits = count(array_unique($this->suits));
        $straightFlushRules = new StraightFlushRules($this->values);
        if ($uniqueSuits == 1) {
            return $straightFlushRules->flushChecker();
        }

        // This was originally a switch case, but it's a bit more readable now.
        $uniqueValues = count(array_unique($this->values));
        if ($uniqueValues == 5) {
            return $straightFlushRules->straightChecker();
        }
        $uniqueValueRules = new UniqueValueRules();
        return $uniqueValueRules->uniqueValueHandsChecker($this->values, $uniqueValues);
    }

    /**
     * Extracts values and suits from cards.
     */
    private function extractor(): void
    {
        foreach ($this->row->getRow() as $card) {
            if ($card) {
                array_push($this->values, $card->getValue());
                array_push($this->suits, $card->getSuit());
            }
        }
    }
}
