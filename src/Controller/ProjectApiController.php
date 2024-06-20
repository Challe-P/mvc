<?php

// En som visar det nuvarande statet.
// En som visar high score.
// En som visar den andra tabellen? - sparade spel eller mest spelade händer
// En som visar sparade spel - om jag gör en sån databas.
// En som visar ett slumpmässigt ifyllt bräde -- kan köra post på denna och den nedre
// En som visar ett av datorn ifyllt bräde - om jag gör datorrundor möjliga
// Visa alla sparade
// Visa en sparad omgångs state
// Spela via json (?)
// Tydligen ska spelarna ha bankkonto - lugnt ju
// Meny med grejer man kan göra

namespace App\Controller;

use App\Entity\Game;
use App\Repository\PlayerRepository;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Project\PokerLogic;
use App\Project\Exceptions\PositionFilledException;
use App\Entity\Player;

class ProjectApiController extends AbstractController
{
    #[Route('/proj/api', name: "projApi")]
    public function projApi(
        SessionInterface $session,
        GameRepository $gameRepository
    ): Response {
        $games = $gameRepository->findAll();
        $currentUser = $session->get('name') ?? "Player";
        $latestBet = 0;
        if ($session->get('game') instanceof PokerLogic) {
            $latestBet = $session->get('game')->bet;
        }
        return $this->render('proj/api.html.twig', ['name' => $currentUser, 'latestBet' => $latestBet, 'games' => $games]);
    }

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
        $response = $this->json($combined);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/proj/api/player/{name}", name: "playerApi")]
    public function playerApi(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        string $name
    ): JsonResponse {
        $player = $playerRepository->findPlayerByName($name);
        $games = [];
        if ($player instanceof Player && $player->getId() != null) {
            $games = $gameRepository->getGamesByPlayer($player->getId());
        }
        $combined = ['Player' => $player, 'Games' => $games];
        $response = $this->json($combined);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/proj/api/game/{id}", name: "gameApi")]
    public function gameApi(
        GameRepository $gameRepository,
        string $id
    ): Response {
        // fixa formateringen på placement
        if (!is_numeric($id)) {
            return $this->redirectToRoute('highscoreApi');
        }
        $id = (int) $id;
        $game = $gameRepository->findGameById($id);
        $response = $this->json($game);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/proj/api/new", name: "newGameApi", methods: ['POST'])]
    public function newGameApi(
        SessionInterface $session,
        Request $request
    ): Response {
        $game = new PokerLogic();
        if (is_numeric($request->get('bet'))) {
            $bet = (int) $request->get('bet');
            $game->bet = $bet;
        }
        $session->set('game', $game);
        $session->set('name', $request->get('name'));
        $session->set('gameEntry', null);
        $session->set('player', null);
        $session->set('api', true);
        return $this->redirectToRoute('update');
    }

    #[Route("/proj/api/game/{id}/{row}:{column}", name: "apiPlay", methods: ['GET'])]
    public function playApi(
        GameRepository $gameRepository,
        SessionInterface $session,
        int $id,
        int $row,
        int $column
    ): Response {
        $gameEntry = $gameRepository->findGameById($id);
        if (!($gameEntry instanceof Game)) {
            return $this->redirectToRoute('highscoreApi');
        }
        return $this->playResolve($gameEntry, $row, $column, $session);
    }

    #[Route("/proj/api/gamepost", name: "playPostApi", methods: ['POST'])]
    public function playPostApi(
        GameRepository $gameRepository,
        SessionInterface $session,
        Request $request
    ): Response {
        if (!is_numeric($request->get('id'))) {
            return $this->redirectToRoute('highscoreApi');
        }
        $id = (int) $request->get('id');
        $gameEntry = $gameRepository->findGameById($id);
        if (!($gameEntry instanceof Game)) {
            return $this->redirectToRoute('highscoreApi');
        }
        $row = $request->get('row');
        $column = $request->get('column');
        $row = is_numeric($row) ? (int) $row : 1;
        $column = is_numeric($column) ? (int) $column : 1;

        return $this->playResolve($gameEntry, $row, $column, $session);
    }

    private function playResolve(
        Game $gameEntry,
        int $row,
        int $column,
        SessionInterface $session
    ): Response {
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
        $session->set('api', true);

        $row = $row - 1;
        $column = $column - 1;
        $game->setCard($row, $column);
        $game->checkScore();
        $session->set('game', $game);
        return $this->redirectToRoute('update');
    }
}
