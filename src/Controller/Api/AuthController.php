<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AuthController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository) {}
//    #[Route('/api/login', name: 'api_login', methods:"POST" )]
    public function apiLogin(Request $request, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $tokenManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Get vars
        $email = $data['email'];
        $password = $data['password'];
        // TODO validate vars

        // Get user
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['message' => 'Invalid credentials'], 401);
        }

        // Validate pw
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid credentials'], 401);
        }

        // Create JWT
        $token = $tokenManager->create($user);

        return $this->json(['token' => $token]);
    }
}
