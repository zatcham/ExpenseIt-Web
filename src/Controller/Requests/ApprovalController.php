<?php

namespace App\Controller\Requests;

use App\Entity\ApprovalComments;
use App\Entity\Budget;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
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
        $data = json_decode($request->getContent(), true);

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

        if (isset($data['comments'])) {
            $comment = new ApprovalComments();
            $comment->setComment($data['comments']);
            $comment->setRequest($request);
            $comment->setUser($user);
            $comment->setTimestamp(new \DateTimeImmutable());
//            $request->setComment($data['comments']);
            $entityManager->persist($comment);
        }

        $request->setStatus($status);
        $entityManager->persist($request);
        $entityManager->flush();
//        Now done in JS
//        $this->addFlash('success', 'Successfully changed approval status to ' . ucfirst($status));
        return new JsonResponse(['status' => 'success', 'id' => $id, 'new_status' => $status, 'old_status' => $currentStatus, 'comments' => $data['comments']],
        200);
    }

    // Change budget via Ajax, if budget is not set, assume department's budget
    #[Route('/approve/change-budget/{id}/{budget}', name: 'approve_change_budget')]
    public function changeBudget(Request $request, string $id, string $budget, EntityManagerInterface $entityManager) : Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated');
        } elseif (!$this->isGranted('ROLE_APPROVAL_RW')) {
            throw $this->createAccessDeniedException('Invalid permissions');
        } // TODO : Permission testing

        // Qualifier used due to import of HttpFoundation\Request
        $requestEnt = $entityManager->getRepository(\App\Entity\Request::class)->find($id);
        if (!$requestEnt) {
//            TODO : Change this from throw to JsonResponse
            throw $this->createNotFoundException('Request not found with id' . $id);
        }

        $budgetEnt = $entityManager->getRepository(Budget::class)->find($budget);
        if (!$budgetEnt) {
            throw $this->createNotFoundException('Budget not found with id' . $budget);
        }

        // Check if we are actually changing it, let the user know if not
        // TODO : is this working?
        if ($budgetEnt == $requestEnt->getBudget()) {
            return new JsonResponse(['status' => 'no-change', 'message' => 'Budget not changed'], 304);
        }

        // Make sure budget is in the request's department // TODO : Is this right?
        if ($budgetEnt->getDepartment() !== $requestEnt->getDepartment()) {
            return new JsonResponse(['status' => 'no-change', 'message' => 'Budget is not in request\'s department'], 400);
        }

        $requestEnt->setBudget($budgetEnt);
        $entityManager->persist($requestEnt);
        $entityManager->flush();
        return new JsonResponse(['status' => 'success', 'budget' => $budgetEnt->getName()], 200);
    }

    /**
     * @return PersistentCollection
     */
    private function getRequests(): PersistentCollection {
        $requests = array();
        $user = $this->getUser();
        if (in_array('ROLE_FINANCE_ADMIN', $user->getRoles())) {
            foreach ($user->getCompany()->getDepartments() as $department) {
                foreach ($department->getRequests() as $request) {
                    $requests[] = $request;
                }
            }
        } else {
            $requests = $user->getDepartment()->getRequests();
        }

        return $requests;
    }

}