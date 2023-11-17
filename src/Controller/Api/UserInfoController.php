<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserInfoController extends AbstractController
{
    #[Route('/api/user_info', name: 'api_user_info')]
    public function getUserInfo(): JsonResponse
    {
        $user = $this->getUser();

        return $this->json([
            'roles' => $user->getRoles(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'job_title' => $user->getJobTitle(),
            'mobile_number' => $user->getMobileNumber(),
        ]);
    }
}
