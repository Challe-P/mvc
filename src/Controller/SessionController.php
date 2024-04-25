<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: "session")]
    public function session(
        SessionInterface $session
    ): Response {
        $data = [
          'session' =>  $session->all()
        ];

        return $this->render('session.html.twig', $data);
    }

    #[Route('/session/delete', name: "sessionDelete")]
    public function clear(
        SessionInterface $session
    ): Response {
        $session->clear();
        $this->addFlash(
            'notice',
            'The session was deleted'
        );
        return $this->redirectToRoute('session');
    }
}
