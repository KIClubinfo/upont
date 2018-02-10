<?php

namespace App\Form;

use App\Entity\Facegame;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class FacegameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('promo')
            ->add('duration')
            ->add('hardcore');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Facegame::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
