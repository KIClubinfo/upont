<?php

namespace KI\UpontBundle\Form\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


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
            ->add('image', 'imageuploader_selector')
            ->add('banner', 'imageuploader_selector');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Users\Club'
        ));
    }

    public function getName()
    {
        return '';
    }
}
