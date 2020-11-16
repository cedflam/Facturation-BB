<?php

namespace App\Controller;

use App\Entity\Estimate;
use App\Form\EstimateType;
use App\Repository\CustomerRepository;
use App\Repository\EstimateRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * EstimateController constructor.
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $manager
     * @param EstimateRepository $estimateRepository
     */
    public function __construct(CustomerRepository $customerRepository, EntityManagerInterface $manager, EstimateRepository $estimateRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->estimateRepository = $estimateRepository;
        $this->manager = $manager;
    }

    /**
     * @Route ("/devis/Mes-Devis", name="estimate_list")
     */
    public function listEstimates()
    {

        return $this->render('estimate/estimate_list.html.twig', [
            'estimates' => $this->estimateRepository->findAll()
        ]);
    }

    /**
     * @Route("/devis/consulter/{id}", name="estimate_show")
     * @param Estimate $estimate
     * @return Response
     */
    public function showEstimate(Estimate $estimate)
    {
        return $this->render('estimate/estimate_show.html.twig', [
            'estimate' => $this->estimateRepository->findOneBy(['id' => $estimate])
        ]);
    }

    /**
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
                     ->setState(true)
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
}
