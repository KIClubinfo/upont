<?php

namespace KI\ClubinfoBundle\Form;

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
            ->add('status')
            ->add('fix', null, array(
                'empty_data' => 'Non vu'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'KI\ClubinfoBundle\Entity\Fix',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}
