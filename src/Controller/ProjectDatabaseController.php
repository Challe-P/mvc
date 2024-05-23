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
        $date = new \DateTime();
        $player = $this->playerCheck($session->get('name'), $playerRepository, $doctrine, $session);
        $gameId = null;
        if ($session->get('gameEntry') != null) {
            $gameId = $session->get('gameEntry')->getId();
        }
        $gameEntry = $this->gameEntryCheck($gameId, $gameRepository, $session);
        $game = $session->get('game');
        
        $gameEntry->setPlayerId($player);
        $gameEntry->setDeck($game->deck->printAll());
        $gameEntry->setPlacement((string) $game->mat);
        if ($gameEntry->getBet() == null) {
            $player->setBalance($player->getBalance() - $game->bet);
        }
        $gameEntry->setBet($game->bet);
        $gameEntry->setAmericanScore($game->mat->getScore()[0]);
        $gameEntry->setBritishScore($game->mat->getScore()[1]);
        $gameEntry->setSavedDate($date);
        if ($game->finished) {
            $gameEntry->setFinished($date);
            $winnings = $this->winningsCalculator($game->bet, $game->mat->getScore());
            $gameEntry->setWinnings($winnings);
            $player->setBalance($player->getBalance() + $winnings);
        }

        $entityManager->persist($player);
        $entityManager->persist($gameEntry);
        $entityManager->flush();
        $session->set('gameEntry', $gameEntry);
        if ($session->get('api')) {
            $url = $this->generateUrl('gameApi', ['id' => $gameEntry->getId()]);
            error_log($url);
            return $this->redirect($url);
        }
        return $this->redirectToRoute('projPlay');
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
        return 0;
    }

    #[Route('/proj/highscore', name: "highscore", methods: ["GET"])]
    public function highscore(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository
    ): Response {
        
        $players =  $playerRepository->findAll();
        // Sort by descending balance
        usort($players, function ($a, $b) {return $a->getBalance() < $b->getBalance();});
        
        $games = $gameRepository->findAll();
        // Sort by descending winnings
        usort($games, function ($a, $b) {return $a->getAmericanScore() < $b->getAmericanScore();});

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
        $games = $gameRepository->getGamesByPlayer($player->getId());
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
        $gameEntry = $gameRepository->findGameById($request->get('id'));
        $game = new PokerLogic($gameEntry->getDeck(), $gameEntry->getPlacement(), $gameEntry->getBet());
        $session->set('game', $game);
        $session->set('name', $gameEntry->getPlayerId()->getName());
        $session->set('gameEntry', $gameEntry);
        return $this->redirectToRoute('projPlay');
    }
}
