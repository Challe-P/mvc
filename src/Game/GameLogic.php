<?php

namespace App\Game;

use App\Game\CardHand;
use App\Game\DeckOfCards;
use App\Game\Rules;
use App\Game\Player;
use OutOfBoundsException;

/**
 * A class that handles the logic for the twenty one-game.
 */
class GameLogic
{
    private Rules $rules;

    public function __construct()
    {
        $this->rules = new Rules();
    }

    /**
     * Does different things depending on the state the game is in.
     * @param array<Player> $players
     * @return array{0: Player[], 1: string}
     */
    public function play(array $players, DeckOfCards $deck, string $state): array
    {
        // Could be made to accommodate more players.
        if ($state == "first" || $state == "draw") {
            return $this->playerTurn($players, $deck, $state);
        }
        if ($state == "hold" || $state == "bank") {
            return $this->bankTurn($players, $deck, $state);
        }
        return [$players, "none"];
    }

    /**
     * The players turn.
     * @param array<Player> $players
     * @return array{0: Player[], 1: string}
     */
    private function playerTurn(array $players, DeckOfCards $deck, string $state)
    {
        if ($state == "first") {
            // Creates a new player if it's the first time.
            $player1 = new Player('player', new CardHand(0, $deck));
            $players = [];
            array_push($players, $player1);
        }
        $player = $this->findPlayerByName($players, "player");
        $player->getHand()->draw($deck);
        $player->setScore($this->rules->translator($player->getHand()));
        if ($player->getScore() == 0) {
            return [$players, "Bank wins"];
        }
        return [$players, $state];

    }

    /**
     * Finds the player with the specified name. Raises error if it is not found.
     * @param array<Player> $players The array of players.
     * @param string $name The name of the player you are looking for.
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
     * Checks who the winner is.
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
     * The banks turn.
     * @param array<Player> $players
     * @return array{0: Player[], 1: string}
     */
    private function bankTurn(array $players, DeckOfCards $deck, string $state): array
    {
        if ($state == "hold") {
            // Creates a new bank player if it's the hold state.
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
