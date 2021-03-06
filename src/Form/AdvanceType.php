<?php

namespace App\Form;

use App\Entity\Advance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => "Montant de l'acompte",
            ])
            ->add('meansPayment', TextType::class, [
                'attr' => [
                    'placeholder' => "Moyen de paiement (chq n° 123) "
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advance::class,
        ]);
    }
}
