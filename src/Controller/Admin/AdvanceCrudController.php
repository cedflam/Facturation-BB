<?php

namespace App\Controller\Admin;

use App\Entity\Advance;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AdvanceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Advance::class;
    }

    public function configureFields(string $pageName): iterable
    {

        return [
            TextField::new('invoice', 'Client'),
            DateField::new('createdAt', 'Date acompte'),
            NumberField::new('amount', 'Montant TTC'),
            TextField::new('meansPayment', 'Moyen de paiement'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Acompte')
            ->setEntityLabelInPlural('Acomptes')
            ->setPageTitle('index', 'Liste des %entity_label_plural%')

            ;
    }

    /**
     * Permet de désactiver les actions
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::EDIT);
    }

}
