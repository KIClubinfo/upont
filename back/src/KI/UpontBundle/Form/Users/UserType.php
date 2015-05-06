<?php

namespace KI\UpontBundle\Form\Users;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
            ))
            ->add('gender')
            ->add('firstName')
            ->add('lastName')
            ->add('nickname')
            ->add('promo')
            ->add('department')
            ->add('origin')
            ->add('nationality')
            ->add('location')
            ->add('phone')
            ->add('skype')
            ->add('statsFoyer')
            ->add('statsPonthub')
            ->add('allowedBde')
            ->add('allowedBds')
            ->add('image', 'imageuploader_selector');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Users\User'
        ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return '';
    }
}
