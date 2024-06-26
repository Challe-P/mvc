<?php

namespace App\Controller;

use App\Project\Exceptions\PositionFilledException;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Project\PokerLogic;
use App\Repository\DatabaseUpdater;

class ProjectController extends AbstractController
{
    /**
     * Renders the starting point for the project sites.
     */
    #[Route("/proj", name: "proj")]
    public function projStart(): Response
    {
        // send in users here as well
        return $this->render('/proj/proj.html.twig');
    }

    /**
     * Route that handles the game.
     */
    #[Route("/proj/play", name: "projPlay")]
    public function projPlay(
        SessionInterface $session
    ): Response {
        $name = $session->get('name');
        $game = $session->get('game');

        if ($name == null || !($game instanceof PokerLogic)) {
            return $this->redirectToRoute('setNameBetForm');
        }
        $game->checkScore();
        return $this->render('/proj/projplay.html.twig', ['name' => $name, 'game' => $game]);
    }

    /**
     * Route to resolve the input from the gameform
     */
    #[Route('/proj/play/resolve', name: "projPlayResolve", methods: ["POST"])]
    public function playResolve(
        SessionInterface $session,
        Request $request,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine
    ): Response {
        $game = $session->get('game');

        if (!($game instanceof PokerLogic)) {
            return $this->redirectToRoute('setNameBetForm');
        }
        $row = $request->get('row');
        $column = $request->get('column');

        if (is_numeric($row) && is_numeric($column)) {
            $row = (int) $row;
            $column = (int) $column;
            try {
                $game->setCard($row, $column);
                $game->checkScore();

                $session->set('game', $game);
                $updater->updateGame($playerRepository, $gameRepository, $doctrine, $session, $game);
            } catch (PositionFilledException) {
                // Do nothing.
            }
        }

        return $this->redirectToRoute('projPlay');
    }

    /**
     * Route for setting name and bet for the game.
     * Will send the users from the database to here as well.
     */
    #[Route('proj/set_namebet', name: 'setNameBetForm', methods: ['GET'])]
    public function setNameBetForm(
        SessionInterface $session
    ): Response {
        $currentUser = $session->get('name');
        $latestBet = 0;
        $game = $session->get('game');
        if ($game instanceof PokerLogic) {
            $latestBet = $game->bet;
        }
        return $this->render('/proj/projname.html.twig', ['name' => $currentUser, 'latestBet' => $latestBet]);
    }

    /**
     * Sets the name, bet and game in the session, and updates the database
     */
    #[Route('proj/set_namebet', name: 'setNameBet', methods: ['POST'])]
    public function setNameBet(
        Request $request,
        SessionInterface $session,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine
    ): Response {
        $game = new PokerLogic();
        $bet = 0;
        if (is_numeric($request->get('bet'))) {
            $bet = (int) $request->get('bet');
        }
        $game->bet = $bet;
        $session->set('game', $game);
        $session->set('name', $request->get('name'));
        $session->set('gameEntry', null);
        $session->set('player', null);
        $updater->updateGame($playerRepository, $gameRepository, $doctrine, $session, $game);
        return $this->redirectToRoute('projPlay');
    }

    /**
     * Autofills a poker hand, mainly used for testing reasons, not a very valid strategy.
     */
    #[Route('proj/autofill', name: 'autofill', methods: ["POST"])]
    public function autofill(
        SessionInterface $session,
        DatabaseUpdater $updater,
        PlayerRepository $playerRepository,
        GameRepository $gameRepository,
        ManagerRegistry $doctrine
    ): Response {
        $game = $session->get('game');
        if ($game instanceof PokerLogic) {
            $game->autofill();
            $session->set('game', $game);
            $updater->updateGame($playerRepository, $gameRepository, $doctrine, $session, $game);
        }
        return $this->redirectToRoute('projPlay');
    }

    /**
     * Route for the music player
     */
    #[Route('proj/music', name: 'musicplayer')]
    public function musicplayer(): Response
    {
        return $this->render('/proj/musicplayer.html.twig');
    }

    /**
     * Route for the about page.
     */
    #[Route('proj/about', name: 'aboutProj')]
    public function aboutProj(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    /**
     * Route for the database about page.
     */
    #[Route('proj/about/database', name: 'aboutDatabase')]
    public function aboutProjDatabase(): Response
    {
        return $this->render('proj/database.html.twig');
    }

    /**
     * Route for the api landing page.
     */
    #[Route('/proj/api', name: "projApi")]
    public function projApi(
        SessionInterface $session,
        GameRepository $gameRepository,
        PlayerRepository $playerRepository
    ): Response {
        $games = $gameRepository->findAll();
        $players = $playerRepository->findAll();
        $currentUser = $session->get('name') ?? "Player";
        $latestBet = 0;
        // If there's a game in the session, get it's bet to use as a defualt in the form
        if ($session->get('game') instanceof PokerLogic) {
            $latestBet = $session->get('game')->bet;
        }
        return $this->render('proj/api.html.twig', ['name' => $currentUser, 'latestBet' => $latestBet, 'games' => $games, 'players' => $players]);
    }
}
