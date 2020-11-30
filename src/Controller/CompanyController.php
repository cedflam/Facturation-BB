<?php

namespace App\Controller;

use App\Repository\AdvanceRepository;
use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    //Propriétés
    private InvoiceRepository $invoiceRepository;
    private AdvanceRepository $advanceRepository;

    /**
     * CompanyController constructor.
     * @param InvoiceRepository $invoiceRepository
     * @param AdvanceRepository $advanceRepository
     */
    public function __construct(InvoiceRepository $invoiceRepository, AdvanceRepository $advanceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->advanceRepository = $advanceRepository;
    }

    /**
     * Affiche le récapitulatif du CA de l'entreprise
     *
     * @Route("/company/chiffre-d-affaire/imprimer", name="company_ca_print")
     * @return Response
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws Exception
     */
    public function index(): Response
    {
        $date = new \DateTime();
        $dateDebut = $date->format('Y-01-01');
        $dateFin = $date->format('Y-12-31');
        $year = $date->format('Y');
        $dateDebutPrevious = date('Y-01-01', strtotime(' -1 years '));
        $dateFinPrevious = date('Y-12-31', strtotime(' -1 years '));

        return $this->render('company/company_ca_print.html.twig', [
            'totalAdvances' => $this->invoiceRepository->findAdvanceByPeriode($dateDebut, $dateFin),
            'totalFacturedRemaining' => $this->invoiceRepository->findTotalRemainingFacturedByPeriode($dateDebut, $dateFin),
            'totalRemaining' => $this->invoiceRepository->findTotalRemainingByPeriode($dateDebut, $dateFin),
            'totalRemainingPreviousYear' => $this->invoiceRepository->findTotalRemainingByPreviousYear($dateDebutPrevious, $dateFinPrevious),
            'advances_list' => $this->advanceRepository->findBy([], ['createdAt' => 'DESC']),
            'invoices_list' => $this->invoiceRepository->findBy([], ['createdAt' => 'DESC']),
            'year' => $year
        ]);
    }
}
