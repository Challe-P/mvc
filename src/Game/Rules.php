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

    public function checkWinner(array $playerArray): string
    {
        uasort($playerArray, function ($a, $b) {
            // Sorteringsfunktion för att hitta vinnare
            if ($a['score'] == $b['score']) {
                // Om det är samma poäng vinner banken.
                if ($a['score'] == 'bank') {
                    return -1;
                } elseif ($b['score'] == 'bank') {
                    return 1;
                } else {
                    return 0;
                }
            }
            // Annars sortera i fallande ordning efter poängen
            return $b['score'] - $a['score'];
        });
        return array_key_first($playerArray);
    }

    public function translator(CardHand $hand): int
    {
        // ess är både ett och 14.
        // points som en array
        // räkna ess, gör ett ballt avdrag sen
        $total = 0;
        $aceCount = 0;
        foreach ($hand->getHand() as $card) {
            $value = $card->getValue();
            if ($value == "ace") {
                $aceCount += 1;
            }
            if (in_array($value, array_keys($this->translation))) {
                $value = $this->translation[$value];
            } else {
                $value = (int)$value;
            }
            $total += $value;
        }
        // Gör istället så att om esset gör dig tjock är det 1.
        // Om det är mer än ett ess är resterande 1.
        if ($aceCount) {
            if ($total + $this->aceMax > $this->maxPoints) {
                $total += $aceCount * $this->aceMin;
            } else {
                $total += $this->aceMax;
                $aceCount -= 1;
                $total += $aceCount * $this->aceMin;
                if ($total > $this->maxPoints) {
                    $total -= 13;
                }
            }
        }

        if ($total > 21) {
            return 0;
        }

        return $total;
    }
}
