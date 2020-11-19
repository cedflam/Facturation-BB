<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
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
                    'class' => "customer"
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
                    'class' => "totalAdvance"
                ]
            ])
            ->add('totalHt', MoneyType::class, [
                'label' => 'Total devis HT',
                'attr' => [
                    'class' => 'totalHT'
                ]
            ])
            ->add('totalTtc', MoneyType::class, [
                'label' => 'Total devis TTC',
                'attr' => [
                    'class' => "totalTTC"
                ]
            ])
            ->add('remainingCapital', MoneyType::class, [
                'label' => 'Restant dû ',
                'attr' => [
                    'class' => 'remainingCapital'
                ]
            ])
        ;

        $builder->get('customer')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvents $event){

            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
