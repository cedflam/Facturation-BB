<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Estimate;
use App\Form\EstimateType;
use App\Repository\CompanyRepository;
use App\Repository\CustomerRepository;
use App\Repository\EstimateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EstimateController extends AbstractController
{
    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * @var EstimateRepository
     */
    private EstimateRepository $estimateRepository;

    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * EstimateController constructor.
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $manager
     * @param EstimateRepository $estimateRepository
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CustomerRepository $customerRepository, EntityManagerInterface $manager, EstimateRepository $estimateRepository, CompanyRepository $companyRepository )
    {
        $this->customerRepository = $customerRepository;
        $this->estimateRepository = $estimateRepository;
        $this->companyRepository = $companyRepository;
        $this->manager = $manager;
    }

    /**
     * Afiche la liste des devis
     *
     * @Route ("/devis/Mes-Devis", name="estimate_list")
     */
    public function listEstimates()
    {
        return $this->render('estimate/estimate_list.html.twig', [
            'estimates' => $this->estimateRepository->findAll()
        ]);
    }

    /**
     * Permet d'afficher un aperçu d'un devis
     *
     * @Route("/devis/consulter/{id}", name="estimate_show")
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
     * @Route("/devis/nouveau", name="estimate_new")
     * @param Request $request
     * @return Response
     */
    public function addEstimate(Request $request): Response
    {
        $estimate = new Estimate();
        $form = $this->createForm(EstimateType::class, $estimate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            // Je lie les descriptions au devis
            foreach ($estimate->getDescriptions() as $description){
                $description->setEstimate($estimate);
                $this->manager->persist($description);
            }
            // J'attribue une référence et un état au devis
            $estimate->setReference(uniqid())
                ->setState(false)
            ;
            $this->manager->persist($estimate);
            $this->manager->flush();

            return $this->redirectToRoute('home');
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

        if($form->isSubmitted() && $form->isValid()){

            // Je lie les descriptions au devis
            foreach ($estimate->getDescriptions() as $description){
                $description->setEstimate($estimate);
                $this->manager->persist($description);
            }

            $this->manager->persist($estimate);
            $this->manager->flush();

            return $this->redirectToRoute('estimate_list');
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
        $customer = $this->customerRepository->findOneBy(['id'=> $estimate->getCustomer()]);

        $html = $this->renderView('estimate/estimate_show.html.twig', [
            'estimate' => $this->estimateRepository->findOneBy(['id' => $estimate]),
            'customer' => $customer,
            'company' => $this->companyRepository->findOneBy(['id' => $estimate->getCustomer()->getCompany()])
        ]);

        return new PdfResponse($pdf->getOutputFromHtml($html), 'devis-'.$customer->getFirstname().'-'.$customer->getLastname().'.pdf');
    }


}
