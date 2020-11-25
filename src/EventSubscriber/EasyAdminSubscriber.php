<?php

namespace App\EventSubscriber;

use App\Entity\Company;
use App\Entity\Customer;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EasyAdminSubscriber extends AbstractController implements EventSubscriberInterface
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
            BeforeEntityPersistedEvent::class => ['linkCustomerWithCompany'],
        ];
    }

    /**
     * Permet d'encoder le mot de passe pès le changement dans l'admin
     *
     * @param BeforeEntityUpdatedEvent $event
     */
    public function setPassword(BeforeEntityUpdatedEvent $event)
    {
        $company = $event->getEntityInstance();

        if (!($company instanceof Company)){
            return;
        }
        $encodedPassword = $this->encoder->encodePassword($company, $company->getPassword());
        $company->setPassword($encodedPassword);
    }

    /**
     * Permet de lier un nouveau customer à la company qui le crée
     *
     * @param BeforeEntityPersistedEvent $event
     */
    public function linkCustomerWithCompany(BeforeEntityPersistedEvent $event)
    {
        $customer = $event->getEntityInstance();

        if (!($customer instanceof Customer)){
            return;
        }

        $customer->setCompany($this->getUser());

    }

}