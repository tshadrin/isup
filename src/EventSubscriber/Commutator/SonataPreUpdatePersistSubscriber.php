<?php
declare(strict_types=1);

namespace App\EventSubscriber\Commutator;

use App\Entity\Commutator\Commutator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;

class SonataPreUpdatePersistSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'sonata.admin.event.persistence.pre_persist' => 'onPreUpdatePersist',
            'sonata.admin.event.persistence.pre_update'=> 'onPreUpdatePersist',
            ];
    }

    /**
     * @param PersistenceEvent $event
     */
    public function onPreUpdatePersist(PersistenceEvent $event): void
    {
        $object = $event->getObject();
        if($object instanceof Commutator)
            $object->onSonataPreUpdatePersist();
    }
}