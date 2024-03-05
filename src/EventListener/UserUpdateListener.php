<?php

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;

class UserUpdateListener
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }
//    #[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: User::class)]
    public function preUpdate(User $user, PreUpdateEventArgs $eventArgs): void
    {
//        $original = $eventArgs->getEntityManager()->getUnitOfWork()->getOriginalEntityData($user);

//        if ($user->getEmail() !== $original['email'] || $user->getFirstname() !== $original['firstname']) {
            $email = (new TemplatedEmail())
            ->from('app@expenseit.tech')
            ->to($user->getEmail())
            ->subject('Your ExpenseIt Account')
            ->htmlTemplate('emails/notify_req_update.html.twig')
            ->context([
                'updated' => 'update',
            ]);
            $this->mailer->send($email);
//        }



        // ...
    }
}
