<?php

namespace App\Controller\Api;

use App\Entity\UserSettings;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserInfoController extends AbstractController
{
    #[Route('/api/user_info', name: 'api_user_info')]
    public function getUserInfo(): JsonResponse
    {
        $user = $this->getUser();
        $fullName = $user->getFirstName() . ' ' . $user->getLastName();
        $imageUrl = 'https://avatar.oxro.io/avatar.svg?name=' . $fullName;
        $userSettings = $user->getUserSettings();
        $settingsArray = array();

//        $ref = new ReflectionClass(UserSettings::class);
//        $properties = $ref->getProperties(\ReflectionProperty::IS_PRIVATE);
//        foreach($properties as $property) {
//            $name = $property->getName();
//            $getter = 'get' . ucfirst($name);
//            if (method_exists($userSettings, $getter)) {
//                $settingValue = $userSettings->$getter();
//                $settingsArray[$name] = $settingValue;
//            }
//        }

        $settingsArray['notifyOnAccept'] = $userSettings->getNotifyOnAccept(); // TODO : Refactor
        $settingsArray['notifyOnUpdate'] = $userSettings->getNotifyOnUpdate();
        $settingsArray['pushNotifs'] = $userSettings->getPushNotifs();
        $settingsArray['mobileAccess'] = $userSettings->getMobileAccess();

        return $this->json([
            'roles' => $user->getRoles(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
//            'company_name' => $user->getCompanyName(),
            'job_title' => $user->getJobTitle(),
            'mobile_number' => $user->getMobileNumber(),
            'profile_picture' => $imageUrl,
            'user_settings' => $settingsArray,
        ]);
    }
}
