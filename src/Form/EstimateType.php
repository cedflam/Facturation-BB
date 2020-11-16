<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Estimate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class EstimateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer', EntityType::class, [
                'label' => "Client",
                'class' => Customer::class,

            ])

            ->add('descriptions', CollectionType::class, [
                'label' => "Prestations",
                'entry_type' => DescriptionType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false
            ])

            ->add('totalHt', MoneyType::class, [
                'label' => "Total HT",
            ])
            ->add('totalTtc', MoneyType::class, [
                'label' => 'Total TTC',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Estimate::class,
        ]);
    }
}
