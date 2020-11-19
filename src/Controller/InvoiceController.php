<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    private EntityManagerInterface $manager;

    /**
     * InvoiceController constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
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
                $advance->setInvoice($invoice);
                $this->manager->persist($advance);
            }
            // J'atribue une nouvelle référence à la facture
            $date = new \DateTime();
            $invoice->setReference($date->format('ymdHi'));
            // Je change la date de la facture
            $invoice->setCreatedAt();
            // Je change le status à payé si le crd est = 0
            if ($invoice->getRemainingCapital() == 0) {
                $invoice->setState(Invoice::FACTURE_REGLEE)
                        ->setTypeInvoice(Invoice::FACTURE_FINALE);
            }else{
                $invoice->setTypeInvoice(Invoice::FACTURE_ACOMPTE);
            }
            // Je passe le devis en accepté
            $estimate = $invoice->getEstimate();
            $estimate->setState(true);

            // J'enregistre
            $this->manager->persist($estimate);
            $this->manager->persist($invoice);
            $this->manager->flush();

            return $this->redirectToRoute('estimate_list');
        }

        return $this->render('invoice/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
