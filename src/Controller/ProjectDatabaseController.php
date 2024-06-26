<?php

namespace App\Controller;

use App\Service\InterfaceHelper;
use App\Repository\PlayerRepository;
use App\Repository\GameRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    /**
     * A route to show the highscore lists.
     */
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
        if ($player instanceof Player && $player->getId() !== null) {
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
    #[Route('/proj/api/delete/{name}', name: 'deletePlayer')]
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
        if ($player instanceof Player && $player->getId() !== null) {
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
