<?php

namespace App\Project;

use App\Project\Exceptions\InvalidRowException;

class UniqueValueRules
{
    /**
     * @var array<string>
     */
    private array $values = [];

    /**
     * @var array<string, array<int>>
     */
    private array $scoreDict = ["Four of a kind" => [50, 16], "Full house" => [25, 10],
    "Straight" => [15, 12], "Three of a kind" => [10, 6],
    "Two pair" => [5, 3], "One pair" => [2, 1]];
    /**
     * Checks the hands that depend on unique values.
     * @param array<string> $values
     * @return array<int>
     */
    public function uniqueValueHandsChecker(array $values, int $uniqueValues): array
    {
        $this->values = $values;

        if ($uniqueValues == 4) {
            return $this->scoreDict["One pair"];
        }
        if ($uniqueValues == 3) {
            return $this->twoPairChecker();
        }
        if ($uniqueValues == 2) {
            return $this->fullHouseChecker();
        }
        throw new InvalidRowException();
    }

    /**
     * Checks for two pairs or three of a kind.
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
        // if any frequency is three - full house, else four of a kind.
        foreach (array_values($countArray) as $value) {
            if ($value == 3) {
                return $this->scoreDict['Full house'];
            }
        }
        return $this->scoreDict['Four of a kind'];
    }
}
