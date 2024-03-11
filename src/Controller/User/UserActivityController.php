<?php

namespace App\Controller\User;

use DH\Auditor\Provider\Doctrine\Persistence\Reader\Reader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserActivityController extends AbstractController
{
    private $auditReader;

    public function __construct(Reader $auditReader) {
        $this->auditReader = $auditReader;
    }

    #[Route('/user/activity', name: 'user_activity_show')]
    public function index(): Response
    {
        $userId = $this->getUser()->getId();
        $audits = $this->auditReader->createQuery('App\Entity\User', [
           'object_id' => $userId
        ])->execute();

        return $this->render('dashboard/user_settings/activity.html.twig', [
            'controller_name' => 'UserActivityController',
            'audits' => $audits,
        ]);
    }
}
