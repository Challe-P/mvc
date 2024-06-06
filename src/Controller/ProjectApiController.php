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

use App\Repository\PlayerRepository;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Project\PokerLogic;

class ProjectApiController extends AbstractController
{
    #[Route('/proj/api', name: "projApi")]
    public function projApi(
        SessionInterface $session,
        GameRepository $gameRepository
    ): Response
    {
        $games = $gameRepository->findAll();
        $currentUser = $session->get('name');
        $latestBet = $session->get('game')->bet ?? 0;
        return $this->render('proj/api.html.twig', ['name' => $currentUser, 'latestBet' => $latestBet, 'games' => $games]);
    }
    #[Route("/proj/api/highscore", name: "highscoreApi")]
    public function highscoreApi(
        PlayerRepository $playerRepository,
        GameRepository $gameRepository
    ): JsonResponse {
        
        $players =  $playerRepository->findAll();
        // Sort by descending balance
        usort($players, function ($a, $b) {return $a->getBalance() < $b->getBalance();});
        
        $games = $gameRepository->findAll();
        // Sort by descending winnings
        usort($games, function ($a, $b) {return $a->getAmericanScore() < $b->getAmericanScore();});
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
        $games = $gameRepository->getGamesByPlayer($player->getId());
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
    ): JsonResponse {
        // fixa formateringen på placement
        $game = $gameRepository->findById($id);
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
        $game->bet = $request->get('bet');
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
        $game = new PokerLogic($gameEntry->getDeck(), $gameEntry->getPlacement(), $gameEntry->getBet());
        $session->set('game', $game);
        $session->set('name', $gameEntry->getPlayerId()->getName());
        $session->set('gameEntry', $gameEntry);
        $session->set('api', true);

        if (is_numeric($row) && is_numeric($column)) {
            $row = (int) $row;
            $column = (int) $column;
            try {
                $game->setCard($row, $column);
                $game->checkScore();
                $session->set('game', $game);
                return $this->redirectToRoute('update');
            } catch (PositionFilledException) {
                // Don't do anything.
            }
        }
        $game->checkScore();
        $url = $this->generateUrl('gameApi', ['id' => $gameEntry->getId()]);
        return $this->redirect($url);
    }

    #[Route("/proj/api/game/post", name: "playPostApi", methods: ['POST'])]
    public function playPostApi(
        GameRepository $gameRepository,
        SessionInterface $session,
        Request $request
    ): Response {
        $gameEntry = $gameRepository->findGameById($request->get('id'));
        $game = new PokerLogic($gameEntry->getDeck(), $gameEntry->getPlacement(), $gameEntry->getBet());
        $session->set('game', $game);
        $session->set('name', $gameEntry->getPlayerId()->getName());
        $session->set('gameEntry', $gameEntry);
        $session->set('api', true);
        $row = $request->get('row');
        $column = $request->get('column');

        if (is_numeric($row) && is_numeric($column)) {
            $row = (int) $row;
            $column = (int) $column;
            try {
                $game->setCard($row, $column);
                $game->checkScore();
                $session->set('game', $game);
                return $this->redirectToRoute('update');
            } catch (PositionFilledException) {
                // Don't do anything.
            }
        }
        $game->checkScore();
        $url = $this->generateUrl('gameApi', ['id' => $gameEntry->getId()]);
        return $this->redirect($url);
    }
}
