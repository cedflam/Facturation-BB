<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CustomerController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class CustomerController extends AbstractController
{
    //Propriétés
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $manager;

    /**
     * CustomerController constructor.
     * @param CustomerRepository $customerRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(CustomerRepository $customerRepository, EntityManagerInterface $manager)
    {
        $this->customerRepository = $customerRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/clients/mes-clients", name="customer_list")
     */
    public function index(): Response
    {
        return $this->render('customer/index.html.twig', [
            'customers' => $this->customerRepository->findAll()
        ]);
    }

    /**
     * Permet d'ajouter un nouveau client
     *
     * @Route("/clients/nouveau", name="customer_new")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addCustomer(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $customer->setCompany($this->getUser());
            $this->manager->persist($customer);
            $this->manager->flush();
            
            $this->addFlash('Opération terminée !', 'Votre client a bien été crée...');

            return $this->redirectToRoute('customer_list');
        }
        
        return $this->render('customer/customer_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier un client
     *
     * @Route ("/clients/modifier/{id}", name="customer_edit")
     *
     * @param Customer $customer
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editCustomer(Customer $customer, Request $request)
    {
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $customer->setCompany($this->getUser());
            $this->manager->persist($customer);
            $this->manager->flush();

            $this->addFlash('Opération terminée !', 'Votre client a bien été mofifié...');

            return $this->redirectToRoute('customer_list');
        }

        return $this->render('customer/customer_new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un client
     *
     * @Route ("/clients/supprimer/{id}", name="customer_delete")
     *
     * @param Customer $customer
     * @return RedirectResponse
     */
    public function deleteCiustomer(Customer $customer)
    {
        $this->manager->remove($customer);
        $this->manager->flush();
        $this->addFlash('Opération Terminée', 'Le client a bien été supprimé !');
        return $this->redirectToRoute('customer_list');
    }
}
