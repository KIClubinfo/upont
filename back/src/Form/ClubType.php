<?php

namespace App\Form;

use App\Selector\ImageSelector;
use App\Entity\Club;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName')
            ->add('name')
            ->add('icon')
            ->add('presentation')
            ->add('active')
            ->add('category')
            ->add('administration')
            ->add('image', ImageSelector::class)
            ->add('banner', ImageSelector::class)
            ->add('place')
            ->add('open');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Club::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
