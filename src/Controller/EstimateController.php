<?php

namespace App\Controller;

use App\Entity\Estimate;
use App\Entity\Invoice;
use App\Form\EstimateType;
use App\Repository\CompanyRepository;
use App\Repository\CustomerRepository;
use App\Repository\EstimateRepository;
use App\Service\ReplaceAccentService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Class EstimateController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class EstimateController extends AbstractController
{
    // Propriétés
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $manager;
    private EstimateRepository $estimateRepository;
    private CompanyRepository $companyRepository;
    private ReplaceAccentService $accentService;

    /**
     * EstimateController constructor.
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $manager
     * @param EstimateRepository $estimateRepository
     * @param CompanyRepository $companyRepository
     * @param ReplaceAccentService $accentService
     */
    public function __construct(
        CustomerRepository $customerRepository,
        EntityManagerInterface $manager,
        EstimateRepository $estimateRepository,
        CompanyRepository $companyRepository,
        ReplaceAccentService $accentService
    )
    {
        $this->customerRepository = $customerRepository;
        $this->estimateRepository = $estimateRepository;
        $this->companyRepository = $companyRepository;
        $this->accentService = $accentService;
        $this->manager = $manager;
    }

    /**
     * Afiche la liste des devis archivés
     *
     * @Route ("/devis/mes-devis-archives", name="estimate_archives_list")
     */
    public function listArchiveEstimates()
    {
        return $this->render('estimate/estimate_archives.html.twig', [
            'estimates' => $this->estimateRepository->findAll()
        ]);
    }

    /**
     * Afiche la liste des devis archivés
     *
     * @Route ("/devis/mes-devis-en-cours", name="estimate_waiting_list")
     */
    public function listWaitingEstimates()
    {
        return $this->render('estimate/estimate_waiting.html.twig', [
            'estimates' => $this->estimateRepository->findAll()
        ]);
    }

    /**
     * Permet d'afficher un aperçu d'un devis
     *
     * @Route("/devis/aperçu/{id}", name="estimate_show")
     * @param Estimate $estimate
     * @return Response
     */
    public function showEstimate(Estimate $estimate)
    {
        return $this->render('estimate/estimate_show.html.twig', [
            'estimate' => $this->estimateRepository->findOneBy(['id' => $estimate]),
            'customer' => $this->customerRepository->findOneBy(['id' => $estimate->getCustomer()]),
            'company' => $this->companyRepository->findOneBy(['id' => $estimate->getCustomer()->getCompany()])
        ]);
    }

    /**
     * Permet de créer un nouveau devis
     *
     * @Route("/devis/nouveau-devis", name="estimate_new")
     * @param Request $request
     * @return Response
     */
    public function addEstimate(Request $request): Response
    {

        $estimate = new Estimate();
        $invoice = new Invoice();
        $form = $this->createForm(EstimateType::class, $estimate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Je récupère la date du jour pour l'intégrer à la référence
            $date = new \DateTime();
            // Je lie les descriptions au devis et à la facture
            foreach ($estimate->getDescriptions() as $description) {
                // Je lie les descriptions au devis
                $description->setEstimate($estimate);
                // Je lie les descriiptions à la facture
                $description->setInvoice($invoice);

                $this->manager->persist($description);

            }
            // J'attribue une référence et un état au devis
            $estimate->setReference($date->format('ymdHi'))
                     ->setState(false)
                     ->setTotalAdvance(0)
                    ->setArchive(false)
            ;
            $this->manager->persist($estimate);

            // Je paramètre une nouvelle facture
            $invoice->setEstimate($estimate)
                    ->setCustomer($estimate->getCustomer())
                    ->setState(Invoice::FACTURE_A_REGLER)
                    ->setTypeInvoice(Invoice::FACTURE_ATTENTE)
                    ->setCreatedAt($estimate->getCreatedAt())
                    ->setReference($date->format('ymdHi'))
                    ->setTotalAdvance(0)
                    ->setTotalHt($estimate->getTotalHt())
                    ->setTotalTtc($estimate->getTotalTtc())
                    ->setRemainingCapital($estimate->getTotalTtc())
                    ->setCreatedAt(new \DateTime())
            ;

            // J'enregisre
            $this->manager->persist($invoice);
            $this->manager->flush();

            $this->addFlash('Bravo !', "Le devis a bien été crée !");

            return $this->redirectToRoute('estimate_waiting_list');
        }
        return $this->render('estimate/index.html.twig', [
            'customers' => $this->customerRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier un devis
     *
     * @Route("/devis/modifier/{id}", name="estimate_edit")
     * @param Request $request
     * @param Estimate $estimate
     * @return Response
     */
    public function editEstimate(Request $request, Estimate $estimate)
    {
        $form = $this->createForm(EstimateType::class, $estimate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Je lie les descriptions au devis
            foreach ($estimate->getDescriptions() as $description) {
                // Je lie la description au devis
                $description->setEstimate($estimate);
                // Je lie la description à la facture
                $description->setInvoice($estimate->getInvoice());

                $this->manager->persist($description);
            }

            $this->manager->persist($estimate);
            $this->manager->flush();

            $this->addFlash('Super !', 'Le devis a bien été modifié.');

            return $this->redirectToRoute('estimate_waiting_list');
        }
        return $this->render('estimate/estimate_edit.html.twig', [
            'customer' => $this->customerRepository->findOneBy(['id' => $estimate->getCustomer()]),
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de générer le devis d'un client au format pdf
     *
     * @Route("/devis/generer/pdf/{id}", name="estimate_generate_pdf")
     *
     * @param Pdf $pdf
     * @param Estimate $estimate
     * @return PdfResponse
     */
    public function generatePdf(PDF $pdf, Estimate $estimate)
    {
        $customer = $this->customerRepository->findOneBy(['id' => $estimate->getCustomer()]);

        $html = $this->renderView('estimate/estimate_show.html.twig', [
            'estimate' => $this->estimateRepository->findOneBy(['id' => $estimate]),
            'customer' => $customer,
            'company' => $this->companyRepository->findOneBy(['id' => $estimate->getCustomer()->getCompany()])
        ]);

        //Je remplace les accents eventuels car non pris en charge
        $firstname = $this->accentService->replaceAccents($customer->getFirstname());
        $lastname = $this->accentService->replaceAccents( $customer->getLastname());

        return new PdfResponse(
            $pdf->getOutputFromHtml($html),
            'devis-'.$lastname.'-'.$firstname.'.pdf'
        );
    }

    /**
     * Permet de passer un devis en status archivé
     *
     * @Route("/devis/archivage/{id}", name="estimate_is_archived" )
     * @param Estimate $estimate
     * @return RedirectResponse
     */
    public function isArchived(Estimate $estimate)
    {
        if ($estimate->getArchive() === true){
            $estimate->setArchive(false);
            $this->addFlash('Opération terminée', 'Votre devis est maintenant disponible !');
            $this->manager->persist($estimate);
            $this->manager->flush();
            return $this->redirectToRoute('estimate_archives_list');

        }else{
            $estimate->setArchive(true);
            $this->addFlash('Opération terminée', 'Votre devis est archivé !');
            $this->manager->persist($estimate);
            $this->manager->flush();
            return $this->redirectToRoute('estimate_waiting_list');
        }
    }

    /**
     * Permet de supprimer un devis
     *
     * @Route ("/devis/supprimer/{id}", name="estimate_remove")
     * @param Estimate $estimate
     * @return RedirectResponse
     */
    public function estimateRemove(Estimate $estimate)
    {
        $this->manager->remove($estimate);
        $this->manager->flush();
        $this->addFlash('Opération Terminée', 'Le devis a bien été supprimé !');
        return $this->redirectToRoute('estimate_waiting_list');
    }
}
