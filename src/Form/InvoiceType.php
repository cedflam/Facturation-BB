<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'label' => "Client",
                'class' => Customer::class,
                'disabled' => true,
                'attr' => [
                    'class' => "customer  ",
                ]
            ])
            ->add('createdAt', DateType::class, [
                'label' => "Date de la facture",
                'widget' => 'single_text',

            ])
            ->add('typeInvoice', ChoiceType::class, [
                'label' => "Etat de la facture",
                'choices' => [
                    'Facture en attente' => 'attente',
                    'Facture finale' => 'finale',
                    "Facture d'acompte" => 'acompte'
                ],

            ])
            ->add('meansPayment', TextType::class, [
                'label' => "Moyen de paiement",
                'required' => false,
                'attr' => [
                    'placeholder' => "Facture finale uniquement...",
                    'class' => 'bg-warning'

                ]
            ])
            ->add('state', ChoiceType::class, [
                'label' => "Facture finale réglée ?",
                'choices' => [
                    'Facture finale non réglée' => false,
                    'Facture finale réglée' => true,
                ],

            ])
            ->add('advances', CollectionType::class, [
                'label' => "Acomptes versés",
                'entry_type' => AdvanceType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])

            ->add('totalAdvance', MoneyType::class, [
                'label' => 'Total des acomptes versés (TTC)',
                'attr' => [
                    'class' => "totalAdvance"
                ]
            ])
            ->add('totalHt', MoneyType::class, [
                'label' => 'Total devis HT',
                'disabled' => true,
                'attr' => [
                    'class' => 'totalHT'
                ]
            ])
            ->add('totalTtc', MoneyType::class, [
                'label' => 'Total devis TTC',
                'disabled' => true,
                'attr' => [
                    'class' => "totalTTC "
                ]
            ])
            ->add('remainingCapital', MoneyType::class, [
                'label' => 'Restant dû ',
                'attr' => [
                    'class' => 'remainingCapital'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
