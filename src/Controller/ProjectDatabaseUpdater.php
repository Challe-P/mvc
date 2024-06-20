<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use App\Repository\GameRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Player;
use App\Entity\Game;
use App\Project\PokerLogic;
use DateTime;

/**
 * A controller class for the project. This controller handles updating and using the database.
 */
class ProjectDatabaseUpdater extends AbstractController
{
    /**
     * Route to create, save and update a finished game.
     */
    #[Route('/proj/update', name: "update", methods: ["GET"])]
    public function updateGame(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine,
        SessionInterface $session
    ): Response {
        // Get and create the relevant data
        $entityManager = $doctrine->getManager();
        $player = new Player();
        if (is_string($session->get('name'))) {
            $player = $this->playerCheck($session->get('name'), $playerRepository, $doctrine, $session);
        }

        $game = $session->get('game');
        if (!$game instanceof PokerLogic) {
            return $this->redirectToRoute('setNameBetForm');
        }

        $gameId = null;
        if ($session->get('gameEntry') instanceof Game) {
            $gameId = $session->get('gameEntry')->getId();
        }
        $gameEntry = $this->gameEntryCheck($gameId, $gameRepository, $session);

        // Set the data in the Game and player objects
        $this->updateGameEntry($gameEntry, $player, $game);

        $entityManager->persist($player);
        $entityManager->persist($gameEntry);
        $entityManager->flush();
        $session->set('gameEntry', $gameEntry);
        if ($session->get('api')) {
            $url = $this->generateUrl('gameApi', ['id' => $gameEntry->getId()]);
            return $this->redirect($url);
        }

        return $this->redirectToRoute('projPlay');
    }

    /**
     * Find a player by name, or create a new one.
     */
    private function playerCheck(
        string $name,
        PlayerRepository $playerRepository,
        ManagerRegistry $doctrine,
        SessionInterface $session
    ): Player {
        $player = $playerRepository->findPlayerByName($name);
        if ($player) {
            $session->set('player', $player);
            return $player;
        }
        $player = new Player();
        $this->playerUpdater($player, $name, 50, $doctrine);
        $session->set('player', $player);
        return $player;
    }

    /**
     * Find a game by id, or create a new one.
     */
    private function gameEntryCheck(
        int $id = null,
        GameRepository $gameRepository,
        SessionInterface $session
    ): Game {
        $gameEntry = $gameRepository->findGameById($id);
        if ($gameEntry) {
            $session->set('gameEntry', $gameEntry);
            return $gameEntry;
        }
        $gameEntry = new Game();
        $session->set('gameEntry', $gameEntry);
        return $gameEntry;
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
     * Updates the Game entry in the database.
     */
    private function updateGameEntry(
        Game $gameEntry,
        Player $player,
        PokerLogic $game
    ): void {
        $date = new DateTime();
        $gameEntry->setPlayerId($player);
        $gameEntry->setDeck($game->deck->printAll());
        $gameEntry->setPlacement((string) $game->mat);
        if ($gameEntry->getBet() == null) {
            $player->setBalance($player->getBalance() - $game->bet);
        }
        $gameEntry->setBet($game->bet ?? 0);
        $gameEntry->setAmericanScore($game->mat->getScore()[0]);
        $gameEntry->setBritishScore($game->mat->getScore()[1]);
        $gameEntry->setSavedDate($date);
        if ($game->finished) {
            $gameEntry->setFinished($date);
            $winnings = $this->winningsCalculator($game->bet ?? 0, $game->mat->getScore());
            $gameEntry->setWinnings($winnings);
            $player->setBalance($player->getBalance() + $game->bet + $winnings);
        }
    }

    /**
     * @param array<int> $score
     */
    private function winningsCalculator(
        int $bet,
        array $score
    ): int {
        if ($score[0] >= 310 || $score[1] >= 120) {
            return $bet * 2;
        }
        if ($score[0] >= 200 || $score[1] >= 70) {
            return $bet;
        }
        return $bet * -1;
    }
}
