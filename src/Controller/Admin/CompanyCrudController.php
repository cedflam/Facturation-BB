<?php

namespace App\Controller\Admin;

use App\Entity\Company;
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
}
