<?php

namespace App\Security\Voter;

use App\Entity\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class RequestVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
//        return in_array($attribute, [self::EDIT, self::VIEW])
//            && $subject instanceof \App\Entity\Request;
//        if (!$subject instanceof Request) {
//            return false;
//        }
//        return true;
//        echo('Attribute: ' . $attribute);
//        echo('Subject Class: ' . get_class($subject));

        return (in_array($attribute, ['view_request']) && $subject instanceof Request);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
//        error_log('Voter called');
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface || !$subject instanceof Request) {
            return false;
        }

        switch($attribute) {
            case 'view_request':
                return $subject->getUser() === $user || (in_array('ROLE_APPROVAL_RW', $user->getRoles()) && $user->getCompany() === $subject->getUser()->getCompany());
        }

//        if ($subject->getUser() === $user) {
//            return true;
//        }
//
//        if (in_array('ROLE_APPROVAL_RW', $user->getRoles()) && $user->getCompany() === $subject->getUser()->getCompany()) {
//            return true;
//        }

        return false;


//        return $subject->getUser() === $user ||
//            (in_array('ROLE_APPROVAL_RW', $user->getRoles()) && $user->getCompany() === $subject->getUser()->getCompany());

    }
}
