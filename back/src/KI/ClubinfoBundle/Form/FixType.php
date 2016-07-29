<?php

namespace KI\ClubinfoBundle\Form;

use KI\ClubinfoBundle\Entity\Fix;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('problem')
            ->add('fix')
            ->add('status', null, [
                'empty_data' => 'Non vu'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'      => Fix::class,
            'csrf_protection' => false
        ]);
    }
}
