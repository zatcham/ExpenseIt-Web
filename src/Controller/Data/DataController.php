<?php

namespace App\Controller\Data;

use App\Repository\RequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController
{
    #[Route('/data/dept', name: 'data_dept_expenditure')]
    public function index(RequestRepository $requestRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'access denied']);
        }

        $deptId = $user->getDepartment()->getId();
//        $data = $deptId;
        $data = $requestRepository->getMonthlyForDepartment($deptId);


        return $this->json($data);
    }
}
