<?php

namespace App\Controller\Data;

use App\Entity\Department;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DataController extends AbstractController // Rename TODO
{
    // "APIs" for use on dashboard, these use session auth and therefore cannot use /api/ path
    #[Route('/data/dept/monthly', name: 'data_dept_expenditure')]
    public function getDeptStatsMonthly(RequestRepository $requestRepository): Response
    {
        if (!$this->getUser()) { // Could use deny unless granted
            return $this->json(['message' => 'Access Denied'], 403);
        }

        $deptId = $this->getUser()->getDepartment()->getId(); // Need to get ID as repository uses that instead of object
        $data = $requestRepository->getMonthlyForDepartment($deptId);

        return $this->json($data); // Json for use with chart js
    }

    #[Route('/data/user/weekly', name: 'data_user_weekly')]
    public function getUserStatsWeekly(RequestRepository $requestRepository) {
        if (!$this->getUser()) {
            return $this->json(['message' => 'Access Denied'], 403);
        }

        $userId = $this->getUser()->getId();
        $data = $requestRepository->getWeeklyForUser($userId);
        return $this->json($data);

    }

    #[Route('/data/percent/user-past-week', name: 'data_percent_past-week')]
    public function getUserPercentPastWeek(RequestRepository $requestRepository) : Response
    {
        if (!$this->getUser()) {
            return $this->json(['message' => 'Access Denied'], 403);
        }
        $userId = $this->getUser()->getId();
        $results = $requestRepository->getPastWeekForUser($userId);
        $percentDiff = $pastWeek = $currentWeek = 0;
        $count = 0;
        foreach($results as $result) {
            if ($count == 0) { // TODO : Fix this in case it breaks
                $pastWeek = $result['total_price'];
                $count++;
            } elseif ($count == 1) {
                $currentWeek = $result['total_price'];
                $count++;
            }

            if ($pastWeek !== 0 && $currentWeek !== 0) {
                $percentDiff = (($currentWeek - $pastWeek) / $pastWeek) * 100;
            } else {
                $percentDiff = 0;
           }
        }
        $percentDiff = round($percentDiff);

        return $this->json($percentDiff);
    }

    #[Route('/data/percent/user-all', name: 'data_percent_all')]
    public function getAllDashPercent(RequestRepository $requestRepository) : Response {
        $percents = array();
        return $this->json($percents);
    }



}
