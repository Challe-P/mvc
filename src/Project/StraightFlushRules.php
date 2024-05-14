<?php

namespace App\Project;

class StraightFlushRules
{
    /**
     * @var array<string>
     */
    private array $values = [];

    /**
     * @var array<int>
     */
    private array $translated = [];

    /**
     * @var array<string, int>
     */
    private array $translation = ['ace' => 1, 'jack' => 11, 'queen' => 12, 'king' => 13];


    /**
     * @var array<string, array<int>>
     */
    private array $scoreDict = ["Royal flush" => [100, 30], "Straight flush" => [75, 30],
    "Flush" => [20, 5], "Straight" => [15, 12]];

    /**
     * @param array<string> $values
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }
    /**
     * Checks a row for flush, straight flush and royal flush
     * @return array<int>
     */
    public function flushChecker(): array
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

    /**
     * Checks a row for a straight or a royal straight, with the use of helper methods.
     * @return array<int>
     */
    public function straightChecker(): array
    {
        list($straightTranslation, $secondTranslation) = $this->translateRow();
        if ($this->straightLoop($straightTranslation) || $this->straightLoop($secondTranslation)) {
            return $this->scoreDict['Straight'];
        }
        return [0, 0];
    }

    /**
     * Loops through the array to check if it's a straight.
     * @param array<int> $translatedArray
     */
    private function straightLoop(array $translatedArray): bool
    {
        if ($translatedArray == []) {
            return false;
        }
        $this->translated = [];
        $ladderValue = $translatedArray[0];
        for ($i = 0; $i < 5; $i++) {
            if ($ladderValue != $translatedArray[$i]) {
                return false;
            }
            array_push($this->translated, (int) $translatedArray[$i]);
            $ladderValue++;
        }
        return true;
    }

    /**
     * @return array<array<int>>
     */
    private function translateRow(): array
    {
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
        return [$straightTranslation, $secondTranslation];
    }
}
