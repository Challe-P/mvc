<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use App\Repository\GameRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Player;
use App\Entity\Game;
use App\Project\PokerLogic;

/**
 * A controller class for the library route.
 */
class ProjectDatabaseController extends AbstractController
{
    // Lite routes här som ska
    // Spara spelet
    // Spara resultat
    // Uppdatera spelarens värde vid start/slut på spel
    // Kanske göra tester först?

    /**
     * Route to save a finished game.
     */
    #[Route('/project/finished', name: "finished", methods: ["POST"])]
    public function registerFinished(
        Request $request,
        ManagerRegistry $doctrine,
        SessionInterface $session,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository
    ): Response {
        $name = $session->get('name');
        $player = $this->playerCheck($name, $playerRepository, $doctrine);
        $game = $session->get('game');
        $this->gameSetter($session->get('id'), $game, $player, $doctrine, $gameRepository);
        return $this->redirectToRoute('project/highscores');
    }

    /**
     * Find a player by name, or create a new one.
     */
    private function playerCheck(
        string $name,
        PlayerRepository $playerRepository,
        ManagerRegistry $doctrine
    ): Player {
        $player = $playerRepository->findPlayerByName($name);
        if ($player) {
            return $player;
        }
        $player = new Player();
        $this->playerUpdater($player, $name, 50, $doctrine);
        return $player;
    }

    /**
     * Find a game by id, or create a new one.
     */
    private function gameCheck(
        int $id,
        GameRepository $gamesRepository
    ): Game {
        $game = $gamesRepository->findGameById($id);
        if ($game) {
            return $game;
        }
        $game = new Game();
        return $game;
    }


    /**
     * Updates a player account
     */
    private function playerUpdater(
        Player $player,
        string $name,
        int $balance,
        ManagerRegistry $doctrine
    ): void {
        $entityManager = $doctrine->getManager();
        if (is_string($name)) {
            $player->setName($name);
        }
        if (is_int($balance)) {
            $player->setBalance($balance);
        }

        $entityManager->persist($player);
        $entityManager->flush();
    }

    /**
     * Updates a game entry.
     */
    private function gameSetter(
        int $id,
        PokerLogic $game,
        Player $player,
        ManagerRegistry $doctrine,
        GameRepository $gameRepository
    ): void {
        $entityManager = $doctrine->getManager();
        $gameEntry = $this->gameCheck($id, $gameRepository);
        $date = new \DateTime();

        $gameEntry->setDeck($game->deck->printAll());
        $gameEntry->setPlacement((string) $game->mat);
        $gameEntry->setBet($game->bet);
        $gameEntry->setAmericanScore($game->mat->getScore()[0]);
        $gameEntry->setBritishScore($game->mat->getScore()[1]);
        $gameEntry->setSavedDate($date);
        if ($game->finished) {
            $gameEntry->setFinished($date);
            $winnings = $this->winningsCalculator($game->bet, $game->mat->getScore());
            $gameEntry->setWinnings($winnings);
            $player->setBalance($player->getBalance() + $winnings);
            $entityManager->persist($player);
        }

        $entityManager->persist($gameEntry);
        $entityManager->flush();
    }

    private function winningsCalculator(
        int $bet,
        array $score
    ): int {
        if ($score[0] >= 310 || $score[1] >= 120) {
            return $bet * 3;
        }
        if ($score[0] >= 200 || $score[1] >= 70) {
            return $bet * 2;
        }
        return $bet * -1;
    }
}
