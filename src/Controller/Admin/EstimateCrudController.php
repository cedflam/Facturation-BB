<?php

namespace App\Controller\Admin;

use App\Entity\Estimate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EstimateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Estimate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Devis')
            ->setEntityLabelInPlural('Devis')
            ->setPageTitle('index', 'Liste des %entity_label_plural%')

            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('customer', 'Clients'),
            NumberField::new('totalTtc', 'TOTAL TTC'),
            DateField::new('createdAt', 'Date Devis'),
            BooleanField::new('archive', 'Devis Archivé ?')
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
