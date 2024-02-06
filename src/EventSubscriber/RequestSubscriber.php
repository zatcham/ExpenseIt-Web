<?php

namespace App\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\Request;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

#[AsEntityListener(event: Events::postUpdate, method: 'postUpdate', entity: Request::class)]
class RequestSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function postUpdate(Request $request, PostUpdateEventArgs $event) {

        $entity = $event->getObject();

        $uow = $event->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        // This seems to be working
        if (array_key_exists('status', $changes)) {
            $email = (new TemplatedEmail())
                ->from(new Address('app@expenseit.tech', 'ExpenseIt'))
                ->to('me@zachm.uk')
                ->subject('An update has been made to your request')
                ->htmlTemplate('emails/notify_req_update.html.twig')
                ->context([
                    'plainPassword' => 'testing',
                    'companyName' => 'none'
                ]);
            $this->mailer->send($email);
        }
    }




//    public function preUpdate(PreUpdateEventArgs $args) : void {
//        $entity = $args->getObject();
//
//        if (!$entity instanceof Request) {
//            return;
//        }
//
//        $changeSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
//
//        if (isset($changeSet['status'])) {
//            $oldStatus = $changeSet['status'][0];
//            $newStatus = $changeSet['status'][1];
//
//            if ($oldStatus != $newStatus) {
//                $email = (new TemplatedEmail())
//                    ->from(new Address('app@expenseit.tech', 'ExpenseIt'))
//                    ->to('me@zachm.uk')
//                    ->subject('Subscriber')
//                    ->htmlTemplate('emails/new_user.html.twig')
//                    ->context([
//                        'plainPassword' => 'testing',
//                        'companyName' => 'none'
//                    ]);
//                $this->mailer->send($email);
//            }
//        }
//    }

    public static function getSubscribedEvents(): array
    {

//        return [
//            'Doctrine\ORM\Events\PreUpdateEvent' => 'onDoctrine\ORM\Events\PreUpdateEvent',
//            Events::preUpdate => 'onPreUpdate'
//        ];
        return [
            Events::postUpdate => 'postUpdate',
        ];
    }
}
