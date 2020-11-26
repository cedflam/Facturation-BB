<?php

namespace App\Form;

use App\Entity\Description;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('prestation',TextareaType::class,[
                'attr' => [
                    'placeholder' => "Détail de la prestation",
                ]
            ])
            ->add('unitPrice', MoneyType::class,[
                'attr' => [
                    'placeholder' => "P.U (HT)",
                ]
            ])
            ->add('quantity', NumberType::class, [
                'attr' => [
                    'placeholder' => "Qté",
                ]
            ])
            ->add('tva', NumberType::class, [
                'attr' => [
                    'placeholder' => "TVA",
                ]
            ])
            ->add('totalHt', MoneyType::class, [
                'attr' => [
                    'placeholder' => "Total HT"
                ]
            ])
            ->add('totalTtc', MoneyType::class, [
                'attr' => [
                    'placeholder' => "Montant TTC",
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Description::class,
        ]);
    }
}
