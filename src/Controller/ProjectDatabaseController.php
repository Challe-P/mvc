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
use DateTime;

/**
 * A controller class for the library route.
 */
class ProjectDatabaseController extends AbstractController
{
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
     * Route to create, save and update a finished game.
     */
    #[Route('/proj/update', name: "update", methods: ["GET"])]
    public function updateGame(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine,
        SessionInterface $session
    ): Response {
        $entityManager = $doctrine->getManager();
        $date = new DateTime();
        $player = new Player();
        if (is_string($session->get('name'))) {
            $player = $this->playerCheck($session->get('name'), $playerRepository, $doctrine, $session);
        }
        $gameId = null;
        if ($session->get('gameEntry') instanceof Game) {
            $gameId = $session->get('gameEntry')->getId();
        }
        $gameEntry = $this->gameEntryCheck($gameId, $gameRepository, $session);
        $game = $session->get('game');
        if (!$game instanceof PokerLogic) {
            return $this->redirect('setNameBetForm');
        }
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

    #[Route('/proj/highscore', name: "highscore", methods: ["GET"])]
    public function highscore(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository
    ): Response {
        $players =  $playerRepository->findAllSorted();
        $games = $gameRepository->findAllSorted();
        return $this->render('proj/highscore.html.twig', ['players' => $players, "games" => $games]);
    }

    /**
     * Shows a specific player, finding it by name.
     */
    #[Route('/proj/player/{name}', name: 'player')]
    public function showPlayerByName(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        string $name
    ): Response {
        $player = $playerRepository->findPlayerByName($name);
        $games = [];
        if ($player instanceof Player && $player->getId() != null) {
            $games = $gameRepository->getGamesByPlayer($player->getId());
        }
        return $this->render('proj/player.html.twig', ['player' => $player, 'games' => $games]);
    }

    /**
     * Loads a game, finding it by id.
     */
    #[Route('/proj/load', name: 'load', methods: ['POST'])]
    public function load(
        GameRepository $gameRepository,
        Request $request,
        SessionInterface $session
    ): Response {
        if (!is_numeric($request->get('id'))) {
            return $this->redirectToRoute('highscore');
        }
        $id = (int) $request->get('id');
        $gameEntry = $gameRepository->findGameById($id);
        if ($gameEntry instanceof Game) {
            $game = new PokerLogic(
                $gameEntry->getDeck() ?? "",
                $gameEntry->getPlacement() ?? "",
                $gameEntry->getBet() ?? 0
            );
            $session->set('game', $game);
            $name = "Player 1";
            if ($gameEntry->getPlayerId() instanceof Player) {
                $name = $gameEntry->getPlayerId()->getName();
            }
            $session->set('name', $name);
            $session->set('gameEntry', $gameEntry);
        }

        return $this->redirectToRoute('projPlay');
    }

    /**
     * Deletes a specific player, finding it by name.
     */
    #[Route('/proj/api/delete/{name}', name: 'player')]
    public function deletePlayerByName(
        ManagerRegistry $doctrine,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        string $name
    ): Response {
        $entityManager = $doctrine->getManager();
        $player = $playerRepository->findPlayerByName($name);

        if (!$player) {
            throw $this->createNotFoundException(
                'No player found with name: ' . $name
            );
        }

        $games = [];
        if ($player instanceof Player && $player->getId() != null) {
            $games = $gameRepository->getGamesByPlayer($player->getId());
        }
        if (is_array($games)) {
            foreach ($games as $game) {
                $entityManager->remove($game);
            }
        }

        $entityManager->remove($player);
        $entityManager->flush();
        return $this->redirectToRoute('highscoreApi');
    }
}
