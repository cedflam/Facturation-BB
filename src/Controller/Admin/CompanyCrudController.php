<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CompanyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Company::class;
    }

  public function configureCrud(Crud $crud): Crud
  {
      return $crud
          ->setEntityLabelInSingular('Entreprise')
          ->setEntityLabelInPlural('Entreprises')
          ->setPageTitle('index', 'Liste des %entity_label_plural%')
          ;
  }

  public function configureFields(string $pageName): iterable
  {
      return [
          TextField::new('name', 'Entreprise'),
          TextField::new('siret', 'Siret'),
          TextField::new('rcs', 'RCS'),
          TextField::new('mention1', 'mention1'),
          TextField::new('mention2', 'mention2'),
          TextField::new('mention3', 'mention3'),
          TextField::new('mention4', 'mention4'),
          TextField::new('mention5', 'mention5'),


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
