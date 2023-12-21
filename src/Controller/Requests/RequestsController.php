<?php

namespace App\Controller\Requests;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends AbstractController
{

    #[Route('/requests/view', name: 'requests_view')]
    public function view(Request $request) : Response {

        return $this->render('dashboard/requests/list.html.twig');
    }

    #[Route('/requests/history', name: 'requests_history')]
    public function history(Request $request) : Response {
        return $this->render('dashboard/requests/history.html.twig');
    }

    #[Route('/requests/detail/{id}', name: 'requests_detail')]
    public function detail(Request $request) : Response {
        return $this->render('dashboard/requests/history.html.twig');
    }

}