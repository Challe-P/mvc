<?php

namespace App\Game;

use App\Game\CardHand;
use App\Game\DeckOfCards;
use App\Game\Rules;
use App\Game\Player;
use OutOfBoundsException;

class GameLogic
{
    private Rules $rules;

    public function __construct()
    {
        $this->rules = new Rules();
    }

    /**
     * @param array<Player> $players
     * @return array{0: Player[], 1: string}
     */
    public function play(array $players, DeckOfCards $deck, string $state): array
    {
        // Could be made to accommodate more players.
        switch ($state) {
            case 'first':
                $player1 = new Player('player', new CardHand(1, $deck));
                $players = [];
                $player1->setScore($this->rules->translator($player1->getHand()));
                array_push($players, $player1);
                return [$players, "first"];
            case 'draw':
                $player = $this->findPlayerByName($players, "player");
                $player->getHand()->draw($deck);
                $player->setScore($this->rules->translator($player->getHand()));
                if ($player->getScore() == 0) {
                    return [$players, "Bank wins"];
                }
                return [$players, "draw"];
            case 'hold':
            case 'bank':
                return $this->bankTurn($players, $deck, $state);
            default:
                return [$players, "none"];
        }
    }
    /**
     * @param array<Player> $players
     */
    private function findPlayerByName(array $players, string $name): Player
    {
        foreach ($players as $player) {
            if ($player->getName() === $name) {
                return $player;
            }
        }
        throw new OutOfBoundsException();
    }

    /**
     * @param array<Player> $players
     */
    private function checkWinner(array $players): string
    {
        $winner = $players[0];
        foreach ($players as $player) {
            if ($player->getScore() > $winner->getScore()) {
                $winner = $player;
            }
        }
        $bank = $this->findPlayerByName($players, 'bank');
        if ($winner->getScore() == $bank->getScore()) {
            return "bank";
        }
        return $winner->getName();
    }

    /**
     * @param array<Player> $players
     * @return array{0: Player[], 1: string}
     */
    private function bankTurn(array $players, DeckOfCards $deck, string $state): array
    {
        if ($state == "hold") {
            $bankPlayer = new Player('bank', new CardHand(0, $deck));
            array_unshift($players, $bankPlayer);
        }
        $bank = $this->findPlayerByName($players, "bank");
        $bank->getHand()->draw($deck);
        $bank->setScore($this->rules->translator($bank->getHand()));
        if ($bank->getScore() >= 17) {
            $winner = ucFirst($this->checkWinner($players));
            return [$players, $winner . " wins"];
        }
        if ($bank->getScore() == 0) {
            return [$players, "Player wins"];
        }
        return [$players, "bank"];
    }
}
