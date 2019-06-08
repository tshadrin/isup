<?php

namespace App\EventSubscriber\Commutator;

use App\Entity\Commutator\Commutator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Sonata\AdminBundle\Event\PersistenceEvent;

class SonataPreUpdatePersistSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'sonata.admin.event.persistence.pre_persist' => 'onPreUpdatePersist',
            'sonata.admin.event.persistence.pre_update'=> 'onPreUpdatePersist',
            ];

    }

    public function onPreUpdatePersist(PersistenceEvent $event)
    {
        $object = $event->getObject();
        if($object instanceof Commutator)
            $object->onSonataPreUpdatePersist();
    }
}