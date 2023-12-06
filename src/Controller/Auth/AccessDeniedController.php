<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccessDeniedController extends AbstractController
{

    #[Route('/error/access-denied', name: 'error_access_denied')]
    public function accessDenied(): Response {
        return $this->render('error/access_denied.html.twig');
    }

}