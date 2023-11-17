<?php

namespace App\Controller\User;

use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserSettingsController extends AbstractController
{
    #[Route('/user/settings', name: 'app_user_settings')]
    public function index(Request $request): Response
    {
        return $this->render('dashboard/user_settings/index.html.twig', [
            'controller_name' => 'UserSettingsController',
        ]);
    }

    #[Route('/user/edit', name: 'user_edit')]
    public function editUser(Request $request, EntityManagerInterface $entityManager) : Response {
        $user = $this->getUser();
        $form = $this->createForm(UserEditType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO : Show current data on form before edit
            // TODO : Check if box is filled / set inputs to non required
            $user->setFirstName($form->get('firstname')->getData());
            $user->setLastName($form->get('lastname')->getData());
            $user->setMobileNumber($form->get('mobile_number')->getData()); // TODO : Transformer to change to +44 format
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

    private function handleImageUpload(User $user): void { // TODO: Blocked
        $image = $user->getImageFile();

        if ($image) {
            $uploader = $this->get('vich_uploader.upload_handler');
            $uploader->upload($user, 'avatars');

            $user->setImageFile(null);
        }
    }
}
