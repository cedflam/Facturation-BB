<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Customer;
use App\Entity\Estimate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bâtiment Buinoud');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Menu');
        yield MenuItem::linkToCrud('Entreprise', 'fa fa-building', Company::class);
        yield MenuItem::linkToCrud('Clients', 'fa fa-users', Customer::class);
        yield MenuItem::linkToCrud('Devis', 'fa fa-file-alt', Estimate::class );
    }
}
