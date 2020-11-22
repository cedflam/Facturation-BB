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
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route ("/facture/consulter/{id}", name="invoice_show")
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
                // J'atribue une date au nouvel acompte
                if (!$advance->getCreatedAt()) {
                    $advance->setCreatedAt(new \DateTime());
                }
                $this->manager->persist($advance);
            }
            // J'atribue une nouvelle référence à la facture
            $date = new \DateTime();
            $invoice->setReference($date->format('ymdHi'));
            // Je change la date de la facture
            $invoice->setCreatedAt($date);

            // Je passe le devis en accepté
            $estimate = $invoice->getEstimate();
            $estimate->setState(Estimate::DEVIS_ACCEPTE);

            // J'enregistre
            $this->manager->persist($estimate);
            $this->manager->persist($invoice);
            $this->manager->flush();

            return $this->redirectToRoute('invoice_list');
        }

        return $this->render('invoice/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de générer une facture d'acompte au format pdf
     *
     * @Route("/facture/generer/pdf/{id}", name="invoice_generate_pdf")
     *
     * @param Pdf $pdf
     * @param Invoice $invoice
     * @return PdfResponse
     */
    public function invoiceGenerateAdvancePdf(PDF $pdf, Invoice $invoice)
    {
        $customer = $this->customerRepository->findOneBy(['id' => $invoice->getCustomer()]);
        $html = $this->renderView('invoice/invoice_show.html.twig', [
            'invoice' => $this->invoiceRepository->findOneBy(['id' => $invoice]),
            'customer' => $customer,
            'company' => $this->companyRepository->findOneBy(['id' => $invoice->getCustomer()->getCompany()])
        ]);

        //Je remplace les accents eventuels car non pris en charge
        $firstname = $this->accentService->replaceAccents($customer->getFirstname());
        $lastname = $this->accentService->replaceAccents($customer->getLastname());

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            'facture-acompte-' . $lastname . '-' . $firstname . '.pdf'
        );
    }
}
