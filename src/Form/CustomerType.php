<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
                'required' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom",
            ])
            ->add('address', TextType::class, [
                'label' => "Adresse"
            ])
            ->add('postCode', TextType::class, [
                'label' => "Code Postal"
            ])
            ->add('city', TextType::class, [
                'label' => "Ville"
            ])
            ->add('email', EmailType::class, [
                'label' => "Adresse email",
                'required' => false
            ])
            ->add('phone', TextType::class, [
                'label' => "Téléphone",
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
