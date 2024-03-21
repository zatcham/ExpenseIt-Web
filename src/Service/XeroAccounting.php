<?php

namespace App\Service;

use App\Controller\Integration\XeroApiController;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\SecurityBundle\Security;
use XeroAPI\XeroPHP\Configuration;
use XeroAPI\XeroPHP\Api\FinanceApi;
use XeroAPI\XeroPHP\Api\AccountingApi;

class XeroAccounting {

    private AccountingApi $accountingApi;
    private ?string $tenantId = null;
    private ?string $accessToken = null;
    private EntityManagerInterface $entityManager;
    private XeroApiController $apiController;

    public function __construct(EntityManagerInterface $entityManager, private Security $security)
    {
        $this->accessToken = $this->getToken(); // Can't use setCredentials as required here
        $config = Configuration::getDefaultConfiguration()->setAccessToken($this->accessToken);
        $this->accountingApi = new AccountingApi(
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

    public function getBudgets() {
        try {
            $budgets = $this->accountingApi->getBudgets($this->tenantId);
        } catch (ApiException $e) {
            exit ($e->getMessage() . ' ' . $this->tenantId . ' ' . $this->accessToken);
            // TODO
        }
        $budgetsArray = [];
        return $budgets;
    }

}