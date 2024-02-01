<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Entity\Request;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RequestSubscriber implements EventSubscriberInterface
{
    private $mailer;

    public function _construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    }

    public function preUpdate(LifecycleEventArgs $args) : void {
        $entity = $args->getObject();

        if (!$entity instanceof Request) {
            return;
        }

        $changeSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Doctrine\ORM\Events\PreUpdateEvent' => 'onDoctrine\ORM\Events\PreUpdateEvent',
        ];
    }
}
