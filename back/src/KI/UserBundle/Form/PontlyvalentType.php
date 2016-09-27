<?php

namespace KI\UserBundle\Form;

use KI\UserBundle\Entity\Pontlyvalent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PontlyvalentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Pontlyvalent::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
