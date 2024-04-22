<?php

namespace Challe_P\Game\Rules;

use Challe_P\Game\Card\Card;
use Challe_P\Game\CardHand\CardHand;

class Rules
{
    private int $maxPoints = 21;
    private array $pointsTranslation = ['jack' => 11, 'queen' => 12, 'king' => 13];
    public function checkWinner(Array $playerArray) : string {
        // Måste ha med namn?
        foreach ($playerArray as $hand) {
            $hand = $this->translator($hand);
        }
        sort($playerArray);

        return $playerArray[0];
    }
    private function translator(CardHand $hand) : int {
        // äss är både ett och 14.
        
    }
}
