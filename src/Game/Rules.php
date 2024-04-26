<?php

namespace Challe_P\Game\Rules;

use Challe_P\Game\Card\Card;
use Challe_P\Game\CardHand\CardHand;

class Rules
{
    private int $maxPoints = 21;
    private array $translation = ['jack' => 11, 'queen' => 12, 'king' => 13];
    private int $aceMax = 14;
    private int $aceMin = 1;

    public function __construct($maxPoints = 0, $translation = [])
    {
        // If you want to use non-standard points
        if ($maxPoints) {
            $this->maxPoints = $maxPoints;
        }
        if ($translation) {
            $this->translation = $translation;
        }
    }

    public function translator(CardHand $hand): int
    {
        $total = 0;
        $aceCount = 0;
        foreach ($hand->getHand() as $card) {
            $value = $card->getValue();
            if ($value == "ace") {
                $aceCount += 1;
            }
            // Ternary operator to elimenate else.
            $newValue = isset($this->translation[$value]) ? $this->translation[$value] : (int)$value;
            $total += $newValue;
        }
        // If there's more than one ace, the rest are one.
        $total = $this->aceChecker($total, $aceCount);

        if ($total > $this->maxPoints) {
            return 0;
        }

        return $total;
    }

    private function aceChecker(int $total, $aceCount): int
    {
        // Checks if any aces, and if they make the hand over the max points at their higher score.
        // If they do, they become worth the lower score.
        if (!$aceCount) {
            return $total;
        }
        if ($total + $this->aceMax > $this->maxPoints) {
            return $total + $aceCount * $this->aceMin;
        }
        $total += $this->aceMax;
        $aceCount -= 1;
        $total += $aceCount * $this->aceMin;
        if ($total > $this->maxPoints) {
            $total -= 13;
        }
        return $total;
    }
}
