<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\Utils;

class TwentyOneApiController extends AbstractController
{
    #[Route('/api/game', name: 'game_api', methods: ['GET'])]
    public function gameApi(
        SessionInterface $session,
        Utils $utils
    ): JsonResponse {
        $output = [];
        $players = $session->get('players');
        if (is_array($players)) {
            foreach ($players as $player) {
                $output[$player->getName()] = ['hand' => $player->getHand()->__toString(),
                'score' => $player->getScore()];
            }
            $state = $session->get('state');
            $cardsLeft = $utils->deckCheck($session)->cardsLeft();
            $output['State'] = $state;
            $output['Cards Left'] = $cardsLeft;
        }
        $response = new JsonResponse($output);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
