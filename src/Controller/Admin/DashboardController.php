<?php

namespace App\Controller\Admin;

use App\Entity\Company;
use App\Entity\Customer;
use App\Entity\Estimate;
use App\Entity\Invoice;
use App\Repository\AdvanceRepository;
use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Driver\Exception;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    // Propriétés
    private InvoiceRepository $invoiceRepository;
    private AdvanceRepository $advanceRepository;

    /**
     * DashboardController constructor.
     * @param InvoiceRepository $invoiceRepository
     * @param AdvanceRepository $advanceRepository
     */
    public function __construct(InvoiceRepository $invoiceRepository, AdvanceRepository $advanceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->advanceRepository = $advanceRepository;
    }

    /**
     * @Route("/admin", name="admin")
     * @return Response
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function index(): Response
    {
        $date = new \DateTime();
        $dateDebut = $date->format('Y-01-01');
        $dateFin = $date->format('Y-12-31');
        $year = $date->format('Y');
        $dateDebutPrevious = date('Y-01-01', strtotime(' -1 years '));
        $dateFinPrevious = date('Y-12-31', strtotime(' -1 years '));

        return $this->render('@EasyAdmin/welcome.html.twig', [
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
            'dashboard_controller_class' => (new \ReflectionClass(static::class))->getShortName(),
            'totalAdvances' => $this->invoiceRepository->findAdvanceByPeriode($dateDebut, $dateFin),
            'totalFacturedRemaining' => $this->invoiceRepository->findTotalRemainingFacturedByPeriode($dateDebut, $dateFin),
            'totalRemaining' => $this->invoiceRepository->findTotalRemainingByPeriode($dateDebut, $dateFin),
            'totalRemainingPreviousYear' => $this->invoiceRepository->findTotalRemainingByPreviousYear($dateDebutPrevious, $dateFinPrevious),
            'advances_list' => $this->advanceRepository->findBy([], ['createdAt' => 'DESC']),
            'invoices_list' => $this->invoiceRepository->findBy([], ['createdAt' => 'DESC']),
            'year' => $year
        ]);
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
        yield MenuItem::linkToCrud('Factures', 'fa fa-file-invoice', Invoice::class);
    }

    /**
     * Permet de generer un pdf récapitulatif du CA de l'année
     *
     * @Route("/admin/generate/CA-PDF", name="admin_generate_pdf")
     *
     * @param Pdf $pdf
     * @param InvoiceRepository $invoiceRepository
     * @return PdfResponse
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function generateStatsPdf(Pdf $pdf, InvoiceRepository $invoiceRepository)
    {
        $date = new \DateTime();
        $dateDebut = $date->format('Y-01-01');
        $dateFin = $date->format('Y-12-31');
        $year = $date->format('Y');
        $dateDebutPrevious = date('Y-01-01', strtotime(' -1 years '));
        $dateFinPrevious = date('Y-12-31', strtotime(' -1 years '));

        $html = $this->renderView('partials/admin/_model_ca_admin.html.twig', [
            'totalAdvances' => $invoiceRepository->findAdvanceByPeriode($dateDebut, $dateFin),
            'totalFacturedRemaining' => $invoiceRepository->findTotalRemainingFacturedByPeriode($dateDebut, $dateFin),
            'totalRemaining' => $invoiceRepository->findTotalRemainingByPeriode($dateDebut, $dateFin),
            'totalRemainingPreviousYear' => $invoiceRepository->findTotalRemainingByPreviousYear($dateDebutPrevious, $dateFinPrevious),
            'advances-list' => $this->advanceRepository->findBy([], ['createdAt' => 'DESC'])
        ]);

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            'CA-' . $year . '.pdf'
        );
    }
}
