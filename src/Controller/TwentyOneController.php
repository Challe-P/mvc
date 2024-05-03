<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\GameController;
use App\Game\GameLogic;
use App\Controller\Utils;

class TwentyOneController extends GameController
{
    #[Route("game/", name:"game", methods: ["GET"])]
    public function game(): Response
    {
        return $this->render('game.html.twig');
    }

    #[Route("game/doc", name:"gameDoc", methods: ["GET"])]
    public function gameDoc(): Response
    {
        return $this->render('gamedoc.html.twig');
    }

    #[Route('game/play', name:"gamePlay", methods: ["GET", "POST"])]
    public function gamePlay(
        SessionInterface $session,
        Request $request,
        Utils $utils
    ): Response {
        $state = $request->get('state') ?? "first";
        $players = [];
        if (is_string($state)) {
            $deck = $utils->deckCheck($session);
            if ($state == "first") {
                $deck->shuffle();
            }
            $players = $utils->playerCheck($session) ?? [];
            if ($deck->cardsLeft() <= 1) {
                $deck->shuffleDrawn($players);
            }
            $gameLogic = new GameLogic();
            list($players, $state) = $gameLogic->play($players, $deck, $state);
            $session->set('players', $players);
            $session->set('state', $state);
            if ($state == "Player wins") {
                $this->addFlash(
                    'win',
                    'Du vann!'
                );
            }
            if ($state == "Bank wins") {
                $this->addFlash(
                    'loss',
                    'Banken vann!'
                );
            }
        }
        return $this->render('gameplay.html.twig', ['hands' => $players, 'state' => $state]);
    }
}
