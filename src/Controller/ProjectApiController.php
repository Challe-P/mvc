<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\PlayerRepository;
use App\Repository\GameRepository;
use App\Repository\DatabaseUpdater;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Project\PokerLogic;
use App\Project\Exceptions\PositionFilledException;
use App\Entity\Player;
use Doctrine\Persistence\ManagerRegistry;

/**
 * A controller that handles all the API routes for the project.
 */
class ProjectApiController extends AbstractController
{
    /**
     * Route for the api highscore page.
     */
    #[Route("/proj/api/highscore", name: "highscoreApi")]
    public function highscoreApi(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository
    ): JsonResponse {
        // Get the players, sorted by descending balance.
        $players =  $playerRepository->findAllSorted();
        // Get the games, sorted by descending american score.
        $games = $gameRepository->findAllSorted();
        $combined = ["Players" => $players, "Games" => $games];
        // Formats the array as a json
        $response = $this->json($combined);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * Route to show a specific players api page.
     */
    #[Route("/proj/api/player/{name}", name: "playerApi")]
    public function playerApi(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        string $name
    ): JsonResponse {
        $player = $playerRepository->findPlayerByName($name);
        $games = [];
        // Assures that there's a Player with the name before accessing the game repository.
        if ($player instanceof Player && $player->getId() !== null) {
            $games = $gameRepository->getGamesByPlayer($player->getId());
        }
        $combined = ['Player' => $player, 'Games' => $games];
        $response = $this->json($combined);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * A route to show a specific game, found by ID.
     */
    #[Route("/proj/api/game/{id}", name: "gameApi")]
    public function gameApi(
        GameRepository $gameRepository,
        string $id
    ): Response {
        $id = (int) $id;
        $game = $gameRepository->findGameById($id);
        $fallback = "No game found";
        // If game is null, fall back to the fallback.
        $game = $game ?? $fallback;
        $response = $this->json($game);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    /**
     * A route to make a new game through the API.
     */
    #[Route("/proj/api/new", name: "newGameApi", methods: ['POST'])]
    public function newGameApi(
        SessionInterface $session,
        Request $request,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine
    ): Response {
        $game = new PokerLogic();
        // Checks if the request is numeric, then casts it to int
        if (is_numeric($request->get('bet'))) {
            $bet = (int) $request->get('bet');
            $game->bet = $bet;
        }
        // Fills the session with all the data available.
        $session->set('game', $game);
        $session->set('name', $request->get('name'));
        // Clears the session
        $session->set('gameEntry', null);
        $session->set('player', null);
        $updater->updateGame($playerRepository, $gameRepository, $doctrine, $session, $game);
        $gameEntry = $session->get('gameEntry');
        // Check that everything went well with the update, otherwise redirects to highscore page.
        if (!($gameEntry instanceof Game)) {
            return $this->redirectToRoute('highscoreApi');
        }
        $url = $this->generateUrl('gameApi', ['id' => $gameEntry->getId()]);
        return $this->redirect($url);
    }

    /**
     * A GET route that you can use to play the game.
     */
    #[Route("/proj/api/game/{id}/{row}:{column}", name: "apiPlay", methods: ['GET'])]
    public function playApi(
        GameRepository $gameRepository,
        SessionInterface $session,
        int $id,
        int $row,
        int $column,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        ManagerRegistry $doctrine
    ): Response {
        $gameEntry = $gameRepository->findGameById($id);
        // If there's no game with the provided ID, redirect to highscore page
        if (!($gameEntry instanceof Game)) {
            return $this->redirectToRoute('highscoreApi');
        }
        return $this->playResolve($gameEntry, $row, $column, $session, $updater, $playerRepository, $gameRepository, $doctrine);
    }

    /**
     * A POST route that you can use to play the game.
     */
    #[Route("/proj/api/gamepost", name: "playPostApi", methods: ['POST'])]
    public function playPostApi(
        GameRepository $gameRepository,
        SessionInterface $session,
        Request $request,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        ManagerRegistry $doctrine
    ): Response {
        // If the provided request isn't numeric, redirect to highscore page.
        if (!is_numeric($request->get('id'))) {
            return $this->redirectToRoute('highscoreApi');
        }
        $id = (int) $request->get('id');
        $gameEntry = $gameRepository->findGameById($id);
        // If there's no game with the provided ID, redirect to highscore page
        if (!($gameEntry instanceof Game)) {
            return $this->redirectToRoute('highscoreApi');
        }
        $row = $request->get('row');
        $column = $request->get('column');

        // If either the row or column isn't numeric, redirect to highscore page
        if (!is_numeric($row) || !is_numeric($column)) {
            return $this->redirectToRoute('highscoreApi');
        }
        $row = (int) $row;
        $column = (int) $column;

        return $this->playResolve($gameEntry, $row, $column, $session, $updater, $playerRepository, $gameRepository, $doctrine);
    }

    /**
     * A function to resolve the plays from the playPostApi and the playApi routes.
     */
    private function playResolve(
        Game $gameEntry,
        int $row,
        int $column,
        SessionInterface $session,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine
    ): Response {
        // Get's the data from the game entry to make the game. I there's no data it makes a new one.
        $game = new PokerLogic(
            $gameEntry->getDeck() ?? "",
            $gameEntry->getPlacement() ?? "",
            $gameEntry->getBet() ?? 0
        );
        $session->set('game', $game);

        // Sets the name to "Player 1" as a default, then checks if the game entry has a player entry.
        $name = "Player 1";
        if ($gameEntry->getPlayerId() instanceof Player) {
            $name = $gameEntry->getPlayerId()->getName();
        }
        $session->set('name', $name);
        $session->set('gameEntry', $gameEntry);

        // If you use the API, the number of the rows and columns are 1 through 5, because that's the non-developer way.
        // For that reason we remove one from the row and column numbers sent in.
        $row = $row - 1;
        $column = $column - 1;
        // Tries to set the card in the chosen row and column, if it is filled, the program moves on.
        try {
            $game->setCard($row, $column);
            $game->checkScore();
            $session->set('game', $game);
            $updater->updateGame($playerRepository, $gameRepository, $doctrine, $session, $game);
        } catch (PositionFilledException) {
            // Do nothing.
        }
        $game->checkScore();
        $url = $this->generateUrl('gameApi', ['id' => $gameEntry->getId()]);
        return $this->redirect($url);
    }
}
