<?php

namespace KI\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('statsFacegame')
            ->add('allowedBde')
            ->add('allowedBds')
            ->add('tour')
            ->add('image', 'image_selector')
            ->add('mailEvent')
            ->add('mailModification')
            ->add('mailShotgun')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UserBundle\Entity\User'
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
