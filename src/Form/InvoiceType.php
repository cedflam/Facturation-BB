<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
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
                    'class' => "customer col-md-4 ",
                ]
            ])
            ->add('typeInvoice', ChoiceType::class, [
                'label' => "Etat de la facture",
                'choices' => [
                    'Facture finale' => 'finale',
                    "Facture d'acompte" => 'acompte'
                ],
                'attr' => [
                    'class' => "col-md-4"
                ]
            ])
            ->add('state', ChoiceType::class, [
                'label' => "Facture finale réglée ?",
                'choices' => [
                    'Facture finale non réglée' => false,
                    'Facture finale réglée' => true,
                ],
                'attr' => [
                    'class' => "col-md-4"
                ]
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
                    'class' => "totalAdvance col-md-7"
                ]
            ])
            ->add('totalHt', MoneyType::class, [
                'label' => 'Total devis HT',
                'disabled' => true,
                'attr' => [
                    'class' => 'totalHT col-md-7'
                ]
            ])
            ->add('totalTtc', MoneyType::class, [
                'label' => 'Total devis TTC',
                'disabled' => true,
                'attr' => [
                    'class' => "totalTTC col-md-7"
                ]
            ])
            ->add('remainingCapital', MoneyType::class, [
                'label' => 'Restant dû ',
                'attr' => [
                    'class' => 'remainingCapital col-md-7 bg-success'
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
