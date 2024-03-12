<?php

namespace App\Controller\Requests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Request as AppRequest;

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
    public function detail(Request $request, string $id, EntityManagerInterface $em) : Response {
        $req = $em->getRepository(AppRequest::class)->find($id);
        $this->denyAccessUnlessGranted('view_request', $req);
        return $this->render('dashboard/requests/detail.html.twig', [
            'request' => $req,
        ]);

    }


    // Maybe move to API controller?
    public function getDailyExpenditure() {
        $user = $this->getUser();
        $dept = $user->getDepartment();
        $sum = 0.00;
        $percent = 0; // TODO

        if (!$dept) { // Issue with users not yet assigned a department
            return 0;
        }

        foreach($dept->getRequests() as $request) {
            if ($this->isWithinLast24($request->getTimestamp())) {
                $sum += $request->getPrice();
            }
        }

        $sum = round($sum, 2); // Ensure rounded to 2dp
//        return $this->json(['sum' => $sum, 'percent' => $percent]);
        return $sum;
    }

    public function getDailySubmissions() {
        // TODO : Monthly instead??

        $count = $previous = $percent = 0;
        $dept = $this->getUser()->getDepartment();
        if (!$dept) {
            return 0;
        }

        foreach($dept->getRequests() as $request) {
            if ($this->isWithinLast24($request->getTimestamp())) {
                $count += 1;
            }
         }


        return $count;
    }


    private function isWithinLast24($timestamp) {
        $now = new \DateTime();
//        $timestamp = new \DateTime($timestamp);
        $diff = $now->diff($timestamp);
        return $diff->days < 1;
    }

    private function isPrevious24($timestamp) {
        $now = new \DateTime();
        $yesterday = clone $now->modify('-1 day');
        $diff = $yesterday->diff($timestamp);

        return $diff->days < 1;
    }


}