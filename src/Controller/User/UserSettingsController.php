<?php

namespace App\Controller\User;

use App\Entity\UserSettings;
use App\Form\UserEditType;
use App\Form\UserSecurityEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class UserSettingsController extends AbstractController
{
//    private $googleAuthenticator;

//    private function __construct(GoogleAuthenticator $googleAuthenticator) {
//        $this->googleAuthenticator = $googleAuthenticator;
//    }

    #[Route('/user/settings', name: 'user_settings')]
    public function index(Request $request): Response
    {

        $fullName = $this->getUser()->getFirstName() . ' ' . $this->getUser()->getLastName();
        $this->getUser()->setFullName($fullName); // Why not update whilst here
        $imageUrl = 'https://avatar.oxro.io/avatar.svg?name=' . $fullName;

        return $this->render('dashboard/user_settings/index.html.twig', [
            'controller_name' => 'UserSettingsController',
            'avatar' => $imageUrl,
        ]);
    }

    #[Route('/user/edit', name: 'user_edit')]
    public function editUser(Request $request, EntityManagerInterface $entityManager) : Response {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class);
        $form->handleRequest($request);

//         TODO : Bug | Set data makes form submitted
//        $form->get('firstname')->setData($user->getFirstName());
//        $form->get('lastname')->setData($user->getLastName());
//        $form->get('mobile_number')->setData($user->getMobileNumber());
//        $form->get('email')->setData($user->getEmail());


        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getUser()->setRoles(['ROLE_ADMIN']);
            // TODO : Show current data on form before edit
            // TODO : Check if box is filled / set inputs to non required
            $user->setFirstName($form->get('firstname')->getData());
            $user->setLastName($form->get('lastname')->getData());
            $user->setMobileNumber($form->get('mobile_number')->getData());
//            $user->setEmail($form->get('email')->getData()); // Bug : Validation error due to email already existing
            $edited = array(); // TODO : Count added items

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Edited successfully');
            // TODO flash alert to show edited items
        }
        return $this->render('dashboard/user_settings/edit.html.twig', [
            'editForm' => $form,
        ]);
    }

    #[Route('/user/security', name:'user_security')]
    public function editUserSecurity(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager) : Response {
        $user = $this->getUser();
        $form = $this->createForm(UserSecurityEditType::class);
        $form->handleRequest($request);
//        $qrCodeUrl = $this->googleAuthenticator->getUrl($user);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData() != "") {
                $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Changed password successfully');
            } else {
                $this->addFlash('success', 'Nothing was changed');
            }
        }
        return $this->render('dashboard/user_settings/edit_security.html.twig', [
            'securityForm' => $form,
        ]);
    }

    #[Route('/user/toggle/{setting}', name:'user_toggle')]
    public function toggleButton(string $setting, EntityManagerInterface $entityManager): JsonResponse {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated');
        }

        $userSettings = $user->getUserSettings();

        $setter = 'set' . ucfirst($setting);
        $getter = 'get' . ucfirst($setting);
        if (!method_exists($userSettings, $setter) || !method_exists($userSettings, $getter)) {
            throw $this->createNotFoundException('Invalid setting provided');
        }

        $userSettings->{$setter}(!$userSettings->{$getter}()); // Toggle setting
//        $userSettings->setNotifyOnAccept(true);
        $entityManager->persist($userSettings);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'toggled' => $userSettings->{$getter}()
        ]);

    }

}
