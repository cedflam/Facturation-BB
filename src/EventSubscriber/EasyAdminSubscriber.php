<?php

namespace App\EventSubscriber;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * EasyAdminChangePasswordSubscriber constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @return array|\string[][]
     */
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => ['setPassword'],
        ];
    }

    /**
     * Permet d'encoder le mot de passe dans le changement d'admin
     *
     * @param BeforeEntityUpdatedEvent $event
     */
    public function setPassword(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Company)){
            return;
        }
        $encodedPassword = $this->encoder->encodePassword($entity, $entity->getPassword());
        $entity->setPassword($encodedPassword);

    }
}