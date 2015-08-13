<?php

namespace KI\ClubinfoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TutoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('text', null, array(
                'empty_data' => 'Tutoriel en cours d\'écriture...'
            ))
            ->add('icon', null, array(
                'empty_data' => 'book'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'KI\ClubinfoBundle\Entity\Tuto',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}
