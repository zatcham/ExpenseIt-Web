<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Entity\UserSettings;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriberInterface
{
    private $hasher;
    private $mailer;

    public function __construct(UserPasswordHasherInterface $hasher, MailerInterface $mailer) {
        $this->hasher = $hasher;
        $this->mailer = $mailer;
    }

    public function beforeEntityPersisted(BeforeEntityPersistedEvent $event): void {
        $user = $event->getEntityInstance();
        $this->generateAndSendPwd($user);
    }

    private function generateAndSendPwd(User $user): void {
        $plainPassword = bin2hex(random_bytes(8));
//        $user->setPassword($plainPassword); // for form submit

        // Test
        $settings = new UserSettings();
        $settings->setNotifyOnAccept(true);
        $user->setUserSettings($settings);
        $user->setPassword($this->hasher->hashPassword($user, $plainPassword));
        $email = (new Email())
            ->from('app@expenseit.tech')
            ->to($user->getEmail())
            ->subject('Your ExpenseIt Account')
            ->text('Password: ' . $plainPassword);
        $this->mailer->send($email);
    }


    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'beforeEntityPersisted',
        ];
    }
}
