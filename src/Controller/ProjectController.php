<?php

namespace App\Controller;

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
        return $this->render('/proj/proj.html.twig');
    }

    #[Route("/proj/play", name: "projPlay")]
    public function projPlay(
        Request $request,
        SessionInterface $session
    ): Response
    {
        if ($request->get('name') != null) {
            $session->set('name', $request->get('name'));
        }
        $name = $session->get('name');
        // if-sats här om inget namn
        // Hämta spel-portalen
        $pokerLogic = $this->gameChecker($session);

        // lägg till kort om det skickats med
        // släng in en try/catch här (PositionFilledException), pga omladdning
        if ($request->get('row') != null && $request->get('column') != null) {
            $pokerLogic->setCard($request->get('row'), $request->get('column'));
            $session->set('game', $pokerLogic);
        }
        $pokerLogic->checkScore();

        return $this->render('/proj/projplay.html.twig', ['name' => $name, 'game' => $pokerLogic]);
    }

    
    #[Route('proj/restart', name: 'restart')]
    public function restart(
        Request $request,
        SessionInterface $session
    ): Response
    {
        if ($this->gameChecker($session) != null)
        {
            $session->set('game', new PokerLogic());
        }
        return $this->redirectToRoute('projPlay');
    }    

    #[Route('proj/music', name: 'musicplayer')]
    public function musicplayer(): Response
    {
        return $this->render('/proj/musicplayer.html.twig');
    }

    private function gameChecker(
        SessionInterface $session
    ): PokerLogic
    {
        if ($session->get('game') === null) {
            return new PokerLogic;
        }
        $game = $session->get('game');
        if ($game instanceof PokerLogic) {
            return $game;
        }
        return new PokerLogic;
    }

    
}
