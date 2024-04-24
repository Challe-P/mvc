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
                $hands['player']['hand'] = new CardHand(1, $deck);
                unset($hands['bank']);
                $hands['player']['score'] = $this->rules->translator($hands['player']['hand']);
                return [$hands, "first"];
            case 'draw':
                $hands['player']['hand']->draw($deck);
                $hands['player']['score'] = $this->rules->translator($hands['player']['hand']);
                if ($hands['player']['score'] == 0) {
                    return [$hands, "Bank wins"];
                }
                return [$hands, "draw"];
            case 'hold':
                $hands['bank']['hand'] = new CardHand(1, $deck);
                $hands['bank']['score'] = $this->rules->translator($hands['bank']['hand']);
                ksort($hands); // Sorterar efter nyckeln så att bank hamnar överst.
                return [$hands, "bank"];
            case 'bank':
                $hands['bank']['hand']->draw($deck);
                $hands['bank']['score'] = $this->rules->translator($hands['bank']['hand']);
                if ($hands['bank']['score'] >= 17) {
                    $winner = ucFirst($this->rules->checkWinner($hands));
                    return [$hands, $winner . " wins"];
                }
                if ($hands['bank']['score'] == 0) {
                    return [$hands, "Player wins"];
                }
                return [$hands, "bank"];
            }
    }
}
