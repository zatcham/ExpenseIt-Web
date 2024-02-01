<?php

namespace App\Controller;

use App\Controller\Api\RequestsApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\Requests\RequestsController;


class HomeController extends AbstractController {

    #[Route('/home', name: 'app_home')]
    public function index(RequestsController $rq): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');



        return $this->render('dashboard/home.html.twig', [
            'daily_exp_gbp' => $rq->getDailyExpenditure(),
            'daily_sub_count' => $rq->getDailySubmissions(),
        ]);
    }

}