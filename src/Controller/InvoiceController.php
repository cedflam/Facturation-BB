<?php

namespace App\Controller;

use App\Entity\Estimate;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\CompanyRepository;
use App\Repository\CustomerRepository;
use App\Repository\EstimateRepository;
use App\Repository\InvoiceRepository;
use App\Service\ReplaceAccentService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InvoiceController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class InvoiceController extends AbstractController
{
    // Prorpriétés
    private EntityManagerInterface $manager;
    private ReplaceAccentService $accentService;
    private InvoiceRepository $invoiceRepository;
    private CustomerRepository $customerRepository;
    private CompanyRepository $companyRepository;
    private EstimateRepository $estimateRepository;

    /**
     * InvoiceController constructor.
     * @param EntityManagerInterface $manager
     * @param ReplaceAccentService $accentService
     * @param InvoiceRepository $invoiceRepository
     * @param CustomerRepository $customerRepository
     * @param CompanyRepository $companyRepository
     * @param EstimateRepository $estimateRepository
     */
    public function __construct(
        EntityManagerInterface $manager,
        ReplaceAccentService $accentService,
        InvoiceRepository $invoiceRepository,
        CustomerRepository $customerRepository,
        CompanyRepository $companyRepository,
        EstimateRepository $estimateRepository
    )
    {
        $this->manager = $manager;
        $this->accentService = $accentService;
        $this->invoiceRepository = $invoiceRepository;
        $this->customerRepository = $customerRepository;
        $this->companyRepository = $companyRepository;
        $this->estimateRepository = $estimateRepository;
    }

    /**
     * @Route ("/factures/consulter/{id}", name="invoice_show")
     * @param Invoice $invoice
     * @return Response
     */
    public function showInvoice(Invoice $invoice)
    {
        return $this->render('invoice/invoice_show.html.twig', [
            'invoice' => $this->invoiceRepository->findOneBy(['id' => $invoice]),
            'customer' => $this->customerRepository->findOneBy(['id' => $invoice->getCustomer()]),
            'company' => $this->companyRepository->findOneBy(['id' => $invoice->getCustomer()->getCompany()])
        ]);
    }

    /**
     * Afiche la liste des factures
     *
     * @Route ("/factures/mes-factures-en-cours", name="invoice_list")
     */
    public function listEstimates()
    {
        return $this->render('invoice/invoice_list.html.twig', [
            'estimates' => $this->estimateRepository->findAll()
        ]);
    }

    /**
     * Afiche la liste des factures
     *
     * @Route ("/factures/mes-factures-archivees", name="invoice_archives_list")
     */
    public function listInvoiceArchives()
    {
        return $this->render('invoice/invoice_archive.html.twig', [
            'invoices' => $this->invoiceRepository->findAll()
        ]);
    }

    /**
     * Permet de facturer  un client
     *
     * @Route("/factures/selection/{id}", name="invoice_select")
     * @param Request $request
     * @param Invoice $invoice
     * @return Response
     */
    public function index(Request $request, Invoice $invoice): Response
    {
        //Je crée le formulaire
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        //Condition de validation du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            //J'attribue les acomptes à la facture en cours
            foreach ($invoice->getAdvances() as $advance) {
                // Je lie l'acompte à la facture
                $advance->setInvoice($invoice)
                        ->setContent("Facture d'acompte sur le devis n° ".$advance->getInvoice()->getEstimate()->getReference())
                ;
                // J'atribue la date saisie dans le formulaire au nouvel acompte
                if (!$advance->getCreatedAt()) {
                    $advance->setCreatedAt($invoice->getCreatedAt());
                }
                $this->manager->persist($advance);
            }

            // J'atribue une nouvelle référence à la facture
            $date = new \DateTime();
            $invoice->setReference($date->format('ymdHis'));

            // Je passe le devis en accepté
            $estimate = $invoice->getEstimate();
            $estimate->setState(Estimate::DEVIS_ACCEPTE);

            // J'enregistre
            $this->manager->persist($estimate);
            $this->manager->persist($invoice);
            $this->manager->flush();

            $this->addFlash('Opération terminée', 'La facture à bien été enregistrée !');

            return $this->redirectToRoute('invoice_list');
        }

        return $this->render('invoice/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une facture archivée
     *
     * @Route ("/facture/archives/supprimer/{id}", name="invoice_archived_delete")
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function invoiceArchivedDelete(Invoice $invoice)
    {
        $this->manager->remove($invoice);
        $this->manager->flush();

        $this->addFlash('Opération réussie', 'La facture archivée a bien été supprimée !');

        return $this->redirectToRoute('invoice_archives_list');

    }

    /**
     * Permet de supprimer une facture
     *
     * @Route ("/facture/supprimer/{id}", name="invoice_delete")
     * @param Invoice $invoice
     * @return RedirectResponse
     */
    public function invoiceDelete(Invoice $invoice)
    {
        $this->manager->remove($invoice);
        $this->manager->flush();

        $this->addFlash('Opération réussie', 'La facture a bien été supprimée !');

        return $this->redirectToRoute('invoice_list');

    }
}
