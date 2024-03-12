<?php

namespace App\Controller\Requests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApprovalController extends AbstractController
{

    #[Route('/approve/list', name: 'approve_list')]
    public function list(Request $request) : Response {
        $requests = $this->getRequests();
        $this->denyAccessUnlessGranted('ROLE_APPROVAL_RW');
        return $this->render('dashboard/approve/list.html.twig',
        [
            'requests' => $requests,
        ]);
    }

    #[Route('/approve/history', name: 'approve_history')]
    public function history(Request $request) : Response {
        $requests = $this->getRequests();
        $this->denyAccessUnlessGranted('ROLE_APPROVAL_RW');
        return $this->render('dashboard/approve/history.html.twig', [
            'requests' => $requests,
        ]);
    }

    #[Route('/approve/status/{id}/{status}', name: 'approve_change_status')]
    public function changeStatus(Request $request, string $id, string $status, EntityManagerInterface $entityManager) : Response {
        $user = $this->getUser();
        $statuses = ['pending', 'refusal', 'accepted', 'note', 'warning']; // List of allowed stati
        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated');
        } elseif (!$this->isGranted('ROLE_APPROVAL_RW')) {
            throw $this->createAccessDeniedException('Invalid permissions');
        }

        $request = $entityManager->getRepository(\App\Entity\Request::class)->find($id);
        if (!$request) {
            throw $this->createNotFoundException('Request not found with id' . $id);
        }
        $currentStatus = $request->getStatus();
        if (!$currentStatus && !$status && in_array($status, $statuses)) { // Check is ok input
            throw $this->createNotFoundException('Status type is invalid');
        }

        $request->setStatus($status);
        $entityManager->persist($request);
        $entityManager->flush();
//        Now done in JS
//        $this->addFlash('success', 'Successfully changed approval status to ' . ucfirst($status));
        return new JsonResponse(['status' => 'success', 'id' => $id, 'new_status' => $status, 'old_status' => $currentStatus]);
    }

    /**
     * @return array
     */
    private function getRequests(): array {
        $requests = array();
        $user = $this->getUser();
        if (in_array('ROLE_FINANCE_ADMIN', $user->getRoles())) {
            foreach ($user->getCompany()->getDepartments() as $department) {
                foreach ($department->getRequests() as $request) {
                    $requests[] = $request;
                }
            }
        } else {
            $requests = $this->getUser()->getDepartment()->getRequests();
        }

        return $requests;
    }

}