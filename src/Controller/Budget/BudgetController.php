<?php

namespace App\Controller\Budget;

use App\Entity\Budget;
use App\Form\BudgetNewType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class BudgetController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->managerRegistry = $managerRegistry;
    }


    #[Route('/budget/view', name: 'budget_view')]
    public function view(): Response
    {
        return $this->render('dashboard/budget/view.html.twig', [
            'budgets' => $this->getAllBudgets()
        ]);
    }

    #[Route('/budget/detail/{id}', name: 'budget_detail')]
    public function detail(Request $request, string $id, EntityManagerInterface $em) : Response {
        $budget = $em->getRepository(Budget::class)->find($id);
        $this->denyAccessUnlessGranted('view_budget', $budget);

        return $this->render('dashboard/budget/detail.html.twig', [
            'budget' => $budget,
        ]);
    }

    #[Route('/budget/new', name: 'budget_new')]
    public function new(Request $request, EntityManagerInterface $em) : Response {
        $user = $this->getUser();
        $form = $this->createForm(BudgetNewType::class, null, [
            'user' => $user
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budget = new Budget();
            $budget->setName($form->get('name')->getData());
            $budget->setTotalBudget($form->get('total_budget')->getData());
            $budget->setPerEmployeeBudget($form->get('per_employee_budget')->getData());
            $budget->setDepartment($form->get('department')->getData());
            $em->persist($budget);
            $em->flush();
            $this->addFlash('success', 'Created new budget "' . $budget->getName() .'" successfully');
        }

        return $this->render('dashboard/budget/new.html.twig', [
            'budgetForm' => $form
        ]);
    }

    // Downloads for export, TODO : S3 upload? Add Time?
    #[Route('/budget/download/xlsx', name: 'budget_download_xlsx')]
    public function downloadXlsx() : BinaryFileResponse {
        $filePath = $this->exportBudgetsAsXlsx($this->getAllBudgets(), $this->getUser()->getCompany()->getName());
        $fileName = basename($filePath);
        // Need to get clean file name as contentDeposition will not accept / or \
        $safeFilename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $fileName);
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $safeFilename);
        return $response;
    }
    // Same as XLSX
    #[Route('/budget/download/csv', name: 'budget_download_csv')]
    public function downloadCsv() : BinaryFileResponse {
        $filePath = $this->exportBudgetsAsCsv($this->getAllBudgets(), $this->getUser()->getCompany()->getName());
        $fileName = basename($filePath);
        $safeFilename = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $fileName);
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $safeFilename);
        return $response;
    }

    // Handle changes from Ajax
    #[Route('/budget/update/{id}/{type}', name: 'budget_update')]
    public function update(Request $request, string $id, string $type, EntityManagerInterface $em) : Response {
        $budget = $em->getRepository(Budget::class)->find($id);
        $user = $this->getUser();
        $allowedTypes = ['delete'];

        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated');
        } elseif (!$this->isGranted('ROLE_APPROVAL_RW')) {
            throw $this->createAccessDeniedException('Invalid permissions');
        }

        if (!in_array($type, $allowedTypes)) {
            return new JsonResponse(['status' => 'faliure', 'message' => 'Type not allowed'], Response::HTTP_BAD_REQUEST);
        }

        if ($type = 'delete') {
            $em->remove($budget);
            $em->flush();
            return new JsonResponse(['status' => 'success', 'message' => 'Deleted succesfully'], Response::HTTP_OK);
        }
        return new JsonResponse(['status' => 'error', 'message' => 'Not allowed'], Response::HTTP_FORBIDDEN);
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

    private function exportBudgetsAsXlsx(array $budgets, string $companyName) : string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $timestamp = date('Y-m-d');
        $filename = "files/exports/$companyName-budgets-$timestamp.xlsx";
        $titles = ['Department', 'Budget Name', 'Total', 'Per Employee'];

        $sheet->fromArray($titles, null, 'A1');
        $rowNo = 2;
        foreach($budgets as $budget) {
            unset($budget['status'], $budget['id']);
            $data = [$budget['department_name'], $budget['name'], $budget['total'], $budget['per_employee']];
            $sheet->fromArray($data, null, "A$rowNo");
            $rowNo++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        return $filename;
    }

    private function exportBudgetsAsCsv(array $budgets, string $companyName) : string {
        $timestamp = date('Y-m-d');
        $filename = "files/exports/$companyName-budgets-$timestamp.csv";
        $titles = ['Department', 'Budget Name', 'Total', 'Per Employee'];
        $file = fopen($filename, 'w');

        fputcsv($file, $titles);
        foreach($budgets as $budget) {
            unset($budget['status'], $budget['id']);
            $data = [$budget['department_name'], $budget['name'], $budget['total'], $budget['per_employee']];
            fputcsv($file, $data);

        }
        fclose($file);
        return $filename;
    }
}
