<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InvoiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invoice::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('customer', 'Clients'),
            DateField::new('createdAt', 'Date dernière facture'),
            NumberField::new('totalAdvance', 'Total Acomptes'),
            NumberField::new('totalTtc', 'Total TTC'),
            NumberField::new('remainingCapital', 'Restant dû'),
            TextField::new('typeInvoice', 'Etat de la facture'),
        ];
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
