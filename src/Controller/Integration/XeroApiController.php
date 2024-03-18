<?php

namespace App\Controller\Integration;

use App\Entity\Companies;
use App\Entity\XeroIntegration;
use App\Service\XeroPayroll;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use XeroAPI\XeroPHP\Api\IdentityApi;
use XeroAPI\XeroPHP\Configuration;


class XeroApiController extends AbstractController
{
    private EntityManagerInterface $em;
    private string $clientId = 'B8ED81145C0244EAA49C63EBBB1F406A';
    private string $clientSecret = 'rMj1kO5tOC96sHrcf2o4XpcpWDFRaw2O1TUH43aCW9BUq7N9';

    private XeroPayroll $xeroPayroll;
    public function __construct(EntityManagerInterface $em, XeroPayroll $xeroPayroll) {
        $this->em = $em;
        $this->xeroPayroll = $xeroPayroll;
    }

    // TODO : Error handling on faliure to auth
    #[Route('/connect/xero', name: 'integrate_xero_connect')]
    public function connectToXero(Request $request) : RedirectResponse {
        $provider = new GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => 'https://localhost:8000/connect/xero/callback', // Change callback before final
            'urlAuthorize' => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken' => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation',
        ]);

        $session = $request->getSession();
        // There should be no code...  TODO : Check if exists
        if (!isset($_GET['code'])) {
            $options = [
                'scope' => ['openid email profile offline_access accounting.transactions accounting.settings payroll.employees']
            ];
            $authUrl = $provider->getAuthorizationUrl($options);
            $session->set('oauth2state', $provider->getState());
            $session->set('test', 'test');
            return new RedirectResponse($authUrl);
        }
        exit ('Something went wrong'); // TODO
    }

    #[Route('/connect/xero/callback', name:'integrate_xero_callback')]
    public function oauthCallback(Request $request) : Response
    {
        $provider = new GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => 'https://localhost:8000/connect/xero/callback',
            'urlAuthorize' => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken' => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
        ]);

        try {
            // Try to get an access token
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->query->get('code'),
            ]);
            $refreshToken = $accessToken->getRefreshToken();

            $userCompany = $this->getUser()->getCompany();
            $this->storeTokens($userCompany, $accessToken, $refreshToken);

            return $this->redirectToRoute('integrate_home');

        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            exit($e->getMessage());
        }
    }

    private function storeTokens(Companies $company, string $accessToken, string $refreshToken) : void {
        $xero = $this->em->getRepository(XeroIntegration::class)->findOneBy(['company' => $company]);

        // Check if entity already exists
        if ($xero) {
            $xero->setAccessToken($accessToken);
            $xero->setRefreshToken($refreshToken);
        } else {
            // Entity doesnt exist, make one
            $xero = new XeroIntegration();
            $xero->setCompany($company);
            $xero->setAccessToken($accessToken);
            $xero->setRefreshToken($refreshToken);
        }
        $xero->setLastUpdated(new \DateTimeImmutable());
        $this->em->persist($xero);
        $this->em->flush();
    }

    #[Route('/connect/xero/token-status', name:'integrate_xero_status')]
    public function checkTokenStatus(Request $request) : JsonResponse {
        $company = $this->getUser()->getCompany();
        $xero = $this->em->getRepository(XeroIntegration::class)->findOneBy(['company' => $company]);
        // TODO : Change from em
        // Check entity exists
        if (!$xero) {
            $this->addFlash('success', 'No xero connection found');
            return $this->json([
                'status' => 'red'
            ]); // TODO : How to handle from service as need bool
//            return $this->redirectToRoute('integrate_home');
        }

        // Get company token
        $accessToken = $xero->getAccessToken(); // We only need access token as refresh is calculated from nbf
//        $refreshToken = $xero->getRefreshToken();

        if (!$this->isTokenExpired('access', $accessToken)) {
            return $this->json([
                'status' => 'ok']);
        } else {
            // TODO
//            if (!$this->isTokenExpired('refresh', $accessToken)) {
//                $this->addFlash('success', 'Refresh token is still valid');
//            } else {
//                $this->addFlash('success', 'Refresh token is invalid');
//            }
            return $this->json([
                'status' => 'refresh']);
        }
    }

    // Returns false if not expired...
    private function isTokenExpired(string $tokenType, $tokenValue) : bool {
        $expiryTime = $tokenType === 'access' ? 1800 : 5184000; // 60 days in secs
        $decodedToken = json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $tokenValue)[1]))), true);
        $currentTime = new \DateTimeImmutable();
        $tokenIssueTime = (new \DateTimeImmutable())->setTimestamp($decodedToken['nbf']);
        $tokenExpiryTime = $tokenIssueTime->modify('+' . $expiryTime . ' seconds');
//        $this->addFlash('success', ucfirst($tokenType) . ' Token expires in ' . $tokenExpiryTime->format('Y-m-d H:i:s'));
        //        return $currentTime;
        return $currentTime > $tokenExpiryTime;
    }

    #[Route('/connect/xero/token', name: 'integrate_xero_token')]
    public function getNewToken(Request $request) : RedirectResponse {
        $provider = new GenericProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'redirectUri' => 'https://localhost:8000/connect/xero/callback',
            'urlAuthorize' => 'https://login.xero.com/identity/connect/authorize',
            'urlAccessToken' => 'https://identity.xero.com/connect/token',
            'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation'
        ]);
        $company = $this->getUser()->getCompany();

        try {
            // Get the stored refresh token
            $refreshToken = $company->getXeroIntegration()->getRefreshToken();

            // Use the refresh token to get a new access token
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $refreshToken
            ]);

            // Store the new access and refresh tokens
            $this->storeTokens($company, $newAccessToken->getToken(), $newAccessToken->getRefreshToken());

            $this->addFlash('success', 'New access and refresh tokens have been saved.');
            return $this->redirectToRoute('integrate_home');

        } catch (IdentityProviderException $e) {
            // Failed to get the access token or user details.
            exit($e->getMessage());
        }
    }

    #[Route('/connect/xero/tenant', name: 'integrate_xero_get-tenant')]
    public function getTenant(Request $request) : Response {
        $company = $this->getUser()->getCompany();
        $accessToken = $company->getXeroIntegration()->getAccessToken();
        if ($this->isTokenExpired('access', $accessToken)) {
            $this->addFlash('success', 'Token expired');
            return $this->redirectToRoute('integrate_home');
        }
        $config = Configuration::getDefaultConfiguration()->setAccessToken($accessToken);
        $identityApi = new IdentityApi(
            new Client(),
            $config
        );
        $result = $identityApi->getConnections();
        foreach($result as $data) {
            $this->addFlash('success', $data);
        }
        $xero = $this->em->getRepository(XeroIntegration::class)->findOneBy(['company' => $company]);
        $xero->setTenantId($result[0]->getTenantId());
        $this->em->persist($xero);
        $this->em->flush();
        return $this->redirectToRoute('integrate_home');
    }

    // No connection found -> Connect
    // Connection Found -> Check Token Validity ->
    // Access Expired -> Refresh -> Store both new tokens
    // If refresh expired -> Connect again

    // Payroll api
    #[Route('/connect/xero/employees', name: 'integrate_xero_get-employees')]
    public function listEmployees(Request $request) {
        $xero = $this->em->getRepository(XeroIntegration::class)->findOneBy(['company' => $this->getUser()->getCompany()]);
        $this->xeroPayroll->setCredentials($xero->getAccessToken(), $xero->getTenantId());
        $employees = $this->xeroPayroll->getEmployees();
        $new = $this->xeroPayroll->syncXeroEmployees();
//        return $this->json($employees);
        return $this->json($new);

    }

}
