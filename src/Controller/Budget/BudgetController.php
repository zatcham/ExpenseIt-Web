<?php

namespace App\Controller\Budget;

use App\Entity\Budget;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BudgetController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->managerRegistry = $managerRegistry;
    }


    #[Route('/budget/view', name: 'budget_view')]
    public function index(): Response
    {
        return $this->render('dashboard/budget/view.html.twig', [
            'controller_name' => 'BudgetController',
            'budgets' => $this->getAllBudgets()
        ]);
    }


    private function getAllBudgets() : array {
        // Working, TODO : Add role-based access
//        $objects = $this->managerRegistry->getRepository(Budget::class)->findAll();
        $company = $this->getUser()->getCompany();
        $deptBudgets = [];

        // Iterate all departments in company
        foreach($company->getDepartments() as $department) {
            // Get all budgets of department
            foreach($department->getBudgets() as $budgets) {
                $b['id'] = $budgets->getId();
                $b['department_name'] = $budgets->getDepartment()->getName();
                $b['name'] = $budgets->getName();
                $b['total'] = $budgets->getTotalBudget();
                $b['per_employee'] = $budgets->getPerEmployeeBudget(); // TODO
                $b['status'] = 'none'; // TODO
                $deptBudgets[] = $b;
            }
        }

        return $deptBudgets;
    }
}
