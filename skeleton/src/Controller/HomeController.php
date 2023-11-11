<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    #[Route('/test')]
    public function number(): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');
        $num = random_int(0, 100);

        return $this->render('number.html.twig', [
            'number' => $num,
        ]);
    }

}