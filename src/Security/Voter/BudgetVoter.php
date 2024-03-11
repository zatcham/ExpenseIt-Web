<?php

namespace App\Security\Voter;

use App\Entity\Budget;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BudgetVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    public function __construct(private Security $security,) {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, ['view_budget']) && $subject instanceof Budget;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface || !$subject instanceof Budget) {
            return false;
        }

        // Allow any super admin users
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case 'view_budget':
                return ($user->getCompany() === $subject->getDepartment()->getCompany() && $this->security->isGranted('ROLE_BUDGET_RW')); // This works, why will it not work with below code...
//                return (in_array('ROLE_BUDGET_RW', $user->getRoles()));
//                return (in_array('ROLE_BUDGET_RW', $user->getRoles()) && $user->getCompany() === $subject->getDepartment()->getCompany());
//                return (in_array('ROLE_BUDGET_RW', $user->getRoles()));

        }

        return false;
    }
}
