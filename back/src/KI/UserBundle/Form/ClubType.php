<?php

namespace KI\UserBundle\Form;

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
            ->add('assos')
            ->add('administration')
            ->add('image', 'image_selector')
            ->add('banner', 'image_selector');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UserBundle\Entity\Club'
        ));
    }

    public function getName()
    {
        return '';
    }
}
