<?php

namespace App\Listener;

use App\Entity\ShippingAddress;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Add data after serialization.
 */
class SerializationListener implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * SerializationListener constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.post_deserialize',
                'class' => ShippingAddress::class,
                'method' => 'onPostDeserializeShippingAddress',
            ]
        ];
    }

    public function onPostDeserializeShippingAddress(ObjectEvent $event)
    {
        /** @var ShippingAddress $entity */
        $entity = $event->getObject();

        if ($this->getTokenStorage()->getToken()) {
            if (!$entity->getUser()) {
                /** @var \App\Entity\User $user */
                $user = $this->getTokenStorage()->getToken()->getUser();
                $entity->setUser($user);
            } else {
                $this->checkOwner($entity);
            }
        }
    }

    /**
     * @return TokenStorageInterface
     */
    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    /**
     * @param ShippingAddress $model
     */
    private function checkOwner(ShippingAddress $model)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getTokenStorage()->getToken()->getUser();
        if ($user !== $model->getUser()) {
            throw new AccessDeniedException();
        }
    }
}
