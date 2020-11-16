<?php

namespace App\Controller\Admin;

use App\Entity\Estimate;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class EstimateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Estimate::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
