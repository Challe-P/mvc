<?php

namespace App\Controller;

use Challe_P\Game\CardHand\CardHand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Challe_P\Game\DeckOfCards\DeckOfCards;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\GameController;
use Challe_P\Game\GameLogic\GameLogic;
use Challe_P\Game\Rules\Rules;
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
        $deck = $utils->deckCheck($session);
        if ($state == "first") {
            $deck->shuffle();
        }
        $hands = $utils->handCheck($session);
        if ($deck->cardsLeft() <= 1) {
            $deck->shuffleDrawn($hands);
        }
        $gameLogic = new GameLogic();
        list($hands, $state) = $gameLogic->play($hands, $deck, $state);
        $session->set('hands', $hands);
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
        return $this->render('gameplay.html.twig', ['hands' => $hands, 'state' => $state]);
    }
}
