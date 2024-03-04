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

        $requests = array();
        $user = $this->getUser();
        if (in_array('ROLE_FINANCE_ADMIN', $user->getRoles())) {
            foreach($user->getCompany()->getDepartments() as $department) {
                foreach($department->getRequests() as $request) {
                    $requests[] = $request;
                }
            }
        } else {
            $requests = $this->getUser()->getDepartment()->getRequests();
        }

        $this->denyAccessUnlessGranted('ROLE_APPROVAL_RW');
        return $this->render('dashboard/approve/list.html.twig',
        [
            'requests' => $requests,
        ]);
    }

}