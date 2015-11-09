<?php

namespace KI\ClubinfoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentraleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product')
            ->add('description')
            ->add('startDate')
            ->add('endDate')
            ->add('name')
            ->add('status', null, array(
                'empty_data' => 'En cours'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'KI\ClubinfoBundle\Entity\Centrale',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}
