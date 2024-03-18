<?php

namespace App\Service;

use App\Controller\Integration\XeroApiController;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use XeroAPI\XeroPHP\ApiException;
use XeroAPI\XeroPHP\Configuration;
use GuzzleHttp\Client;
use XeroAPI\XeroPHP\Api\PayrollUkApi;
use XeroAPI\XeroPHP\Models\PayrollUk\Employee;
use XeroAPI\XeroPHP\Models\PayrollUk\Reimbursement;

class XeroPayroll
{
    private PayrollUkApi $payrollApi;
    private ?String $tenantId = null;
    private ?String $accessToken = null;
    private EntityManagerInterface $entityManager;
    private XeroApiController $apiController;

    public function __construct(EntityManagerInterface $entityManager, private Security $security)
    {
        $this->accessToken = $this->getToken(); // Can't use setCredentials as required here
        $config = Configuration::getDefaultConfiguration()->setAccessToken($this->accessToken);
        $this->payrollApi = new PayrollUkApi(
            new Client(),
            $config
        );
        $this->entityManager = $entityManager;
    }

    private function getToken() {
        $company = $this->security->getUser()->getCompany();
        return $company->getXeroIntegration()->getAccessToken(); // TODO Handle invalid
    }

    public function setCredentials(?string $accessToken, ?string $tenantId) : void {
        $this->accessToken = $accessToken; // TODO : Is this necessary
        $this->tenantId = $tenantId;
    }

    public function getEmployees(): array {
        try {
            $employees = $this->payrollApi->getEmployees($this->tenantId, null, 1);
        } catch (ApiException $e) {
            exit ($e->getMessage() . ' ' . $this->tenantId . ' ' . $this->accessToken);
        }
        $employeeArray = [];
        $i = 1;
        foreach ($employees['employees'] as $employee) {
            $employeeArray[] = [
                'firstName' => $employee->getFirstName(),
                'lastName' => $employee->getLastName(),
                'email' => $employee->getEmail(),
                // ... other fields
                'xeroEmployeeId' => $employee->getEmployeeID(),
            ];
//            $employeeArray['count'] = $i;
            $i++;
        }
        return $employeeArray;
    }

    // Match Xero employee IDs to ExpenseIt IDs
    public function syncXeroEmployees(): array {
        $company = $this->security->getUser()->getCompany();
        $employees = $this->getEmployees();
        $new = [];
        foreach($employees as $employee) {
                if ($employee['email']) {
                    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $employee['email'], 'company' => $company]);
                    if ($user) {
                        $new[] = [
                            'expEmployeeId' => $user->getId(),
                            'xeroEmployeeId' => $employee['xeroEmployeeId'],
                            'firstName' => $employee['firstName'],
                            'lastName' => $employee['lastName'],
                            'email' => $employee['email']
                        ];
                    }
                }
        }
        return $new;
    }

}