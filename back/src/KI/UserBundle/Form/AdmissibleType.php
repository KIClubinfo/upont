<?php

namespace KI\UserBundle\Form;

use KI\UserBundle\Entity\Admissible;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AdmissibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('scei')
            ->add('serie')
            ->add('contact')
            ->add('room')
            ->add('details');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Admissible::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
