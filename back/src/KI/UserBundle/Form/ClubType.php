<?php

namespace KI\UserBundle\Form;

use KI\CoreBundle\Selector\ImageSelector;
use KI\UserBundle\Entity\Club;
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
            ->add('image', ImageSelector::class)
            ->add('banner', ImageSelector::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => Club::class
        ));
    }
}
