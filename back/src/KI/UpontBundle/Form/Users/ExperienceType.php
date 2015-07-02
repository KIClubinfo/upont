<?php

namespace KI\UpontBundle\Form\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('startDate')
            ->add('endDate')
            ->add('description')
            ->add('country')
            ->add('city')
            ->add('latitude')
            ->add('longitude')
            ->add('category')
            ->add('company');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Users\Experience'
        ));
    }

    public function getName()
    {
        return '';
    }
}
