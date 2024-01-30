<?php

namespace App\Controller\Requests;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApprovalController extends AbstractController
{

    #[Route('/approve/list', name: 'approve_list')]
    public function list(Request $request) : Response {
        return $this->render('dashboard/approve/list.html.twig');
    }

}