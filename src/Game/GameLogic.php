<?php

namespace Challe_P\Game\GameLogic;

use Challe_P\Game\Card\Card;
use Challe_P\Game\CardHand\CardHand;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use Challe_P\Game\Rules\Rules;

class GameLogic
{
    private Rules $rules;

    public function __construct(){
        $this->rules = new Rules();
    }

    public function play(array $hands, DeckOfCards $deck, $state) {
        // Could be made to accommodate more players.
        switch ($state) {
            case 'first':
                $hands['player'] = new CardHand(1, $deck);
                unset($hands['bank']);
                return [$hands, 1];
            case 'draw':
                $hands['player']->draw($deck);
                if ($this->rules->translator($hands['player']) == 0) {
                    return [$hands, "Bank wins"];
                }
                return [$hands, 1];
            case 'hold':
                $hands['bank'] = new CardHand(1, $deck);
                return [$hands, 1];
            case 'bank':
                $hands['bank']->draw($deck);
                if ($this->rules->translator($hands['bank']) > 17) {
                    return [$hands, `Winner: ` . $this->rules->checkWinner($hands)];
                }
                if ($this->rules->translator($hands['bank']) == 0) {
                    return [$hands, "Player wins"];
                }
                return [$hands, 1];
            }
    }
}
