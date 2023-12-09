<?php

namespace App\Controller\Auth;

use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class EmailVerificationController extends AbstractController
{

    #[Route('/user/send_verify', name: 'user_resend_verify')]
    public function sendEmail(EmailVerifier $emailVerifier) : JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated');
        }

        if (!$user->isVerified()) {
            $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('app@expenseit.tech', 'ExpenseIt'))
                    ->to($user->getEmail())
                    ->subject('Please verify your email')
                    ->htmlTemplate('emails/verify_email.html.twig')
            );
            return new JsonResponse(['status' => 'success']);
        } else {
            return new JsonResponse(['status' => 'error', 'error' => 'User already verified']);
        }
    }

}