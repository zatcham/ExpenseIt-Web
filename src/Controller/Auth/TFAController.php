<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TFAController extends AbstractController
{
    #[Route('/tfa', name: 'app_tfa')]
    public function index(): Response
    {
        return $this->render('auth/tfa_otp.html.twig', [
            'controller_name' => 'TFAController',
        ]);
    }
}
