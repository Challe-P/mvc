<?php

namespace App\Controller;

use App\Project\Exceptions\PositionFilledException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Project\PokerLogic;

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

    #[Route("/proj/play", name: "projPlay")]
    public function projPlay(
        Request $request,
        SessionInterface $session
    ): Response {
        $name = $session->get('name');
        $pokerLogic = $this->gameChecker($session);

        if ($name == null || $pokerLogic->bet == null)
        {
            return $this->redirectToRoute('setNameBetForm');
        }
        $row = $request->get('row');
        $column = $request->get('column');

        if (is_numeric($row) && is_numeric($column)) {
            $row = (int) $row;
            $column = (int) $column;
            try {
                $pokerLogic->setCard($row, $column);
                $session->set('game', $pokerLogic);
            } catch (PositionFilledException) {
                // Don't do anything.
            }
        }
        $pokerLogic->checkScore();

        return $this->render('/proj/projplay.html.twig', ['name' => $name, 'game' => $pokerLogic]);
    }

    /**
     * Route for setting name and bet for the game.
     * Will send the users from the database to here as well.
     */
    #[Route('proj/set_namebet', name: 'setNameBet', methods: ['GET'])]
    public function setNameBetForm(
        SessionInterface $session
    ): Response {
        $currentUser = $session->get('name');
        $latestBet = $this->gameChecker($session)->bet;
        return $this->render('/proj/projname.html.twig', ['name' => $currentUser, 'latestBet' => $latestBet]);
    }

    #[Route('proj/set_namebet', name: 'setNameBet', methods: ['POST'])]
    public function setNameBet(
        Request $request,
        SessionInterface $session
    ): Response {
        $game = $this->gameChecker($session);
        $game->bet = $request->get('bet');
        $session->set('game', $game);
        $session->set('name', $request->get('name'));
        return $this->render('/proj/projplay.html.twig');
    }
    #[Route('proj/restart', name: 'restart')]
    public function restart(
        SessionInterface $session
    ): Response {
        if ($this->gameChecker($session) != null) {
            $session->set('game', new PokerLogic());
        }
        return $this->redirectToRoute('projPlay');
    }

    #[Route('proj/autofill', name: 'autofill', methods: ["POST"])]
    public function autofill(
        SessionInterface $session
    ): Response {
        $pokerLogic = $this->gameChecker($session);
        $name = $session->get('name');
        $pokerLogic->autofill();
        $pokerLogic->checkScore();
        return $this->render('/proj/projplay.html.twig', ['name' => $name, 'game' => $pokerLogic]);
    }

    #[Route('proj/music', name: 'musicplayer')]
    public function musicplayer(): Response
    {
        return $this->render('/proj/musicplayer.html.twig');
    }

    // I've got a feeling this could be a coelescing operator?
    private function gameChecker(
        SessionInterface $session
    ): PokerLogic {
        if ($session->get('game') === null) {
            return new PokerLogic();
        }
        $game = $session->get('game');
        if ($game instanceof PokerLogic) {
            return $game;
        }
        return new PokerLogic();
    }


}
