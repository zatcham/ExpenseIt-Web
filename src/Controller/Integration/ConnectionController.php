<?php

namespace App\Controller\Integration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConnectionController extends AbstractController
{
    #[Route('/connect/home', name: 'integrate_home')]
    public function index(): Response
    {
        return $this->render('integration/connection/index.html.twig', [
            'controller_name' => 'ConnectionController',
        ]);
    }

}
