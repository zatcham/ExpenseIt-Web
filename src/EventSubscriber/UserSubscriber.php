<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\UserSettings;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private $hasher;
    private $mailer;
    private $security;

    public function __construct(UserPasswordHasherInterface $hasher, MailerInterface $mailer, Security $security) {
        $this->hasher = $hasher;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public function beforeEntityPersisted(BeforeEntityPersistedEvent $event): void {
        $user = $event->getEntityInstance();
        $this->generateAndSendPwd($user);
        $this->setCompany($user);
    }

    private function setCompany(User $user): void {
        $currentUser = $this->security->getUser();
        $company = $currentUser->getCompany();
        $user->setCompany($company);
    }

    private function generateAndSendPwd(User $user): void {
        $plainPassword = bin2hex(random_bytes(8));
//        $user->setPassword($plainPassword); // for form submit

        // Test
        $settings = new UserSettings();
        $settings->setNotifyOnAccept(true);
        $user->setUserSettings($settings);
        $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
//        $email = (new Email())
//            ->from('app@expenseit.tech')
//            ->to($user->getEmail())
//            ->subject('Your ExpenseIt Account')
//            ->text('Password: ' . $plainPassword);
        $email = (new TemplatedEmail())
            ->from(new Address('app@expenseit.tech', 'ExpenseIt'))
            ->to($user->getEmail())
            ->subject('Your ExpenseIt Account')
            ->htmlTemplate('emails/new_user.html.twig')
            ->context([
                'plainPassword' => $plainPassword,
                'companyName' => $user->getCompanyName()
            ]);

        $this->mailer->send($email);
    }


    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforeEntityPersisted',
        ];
    }
}
