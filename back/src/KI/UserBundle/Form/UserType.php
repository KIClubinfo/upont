<?php

namespace KI\UserBundle\Form;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use KI\CoreBundle\Selector\ImageSelector;
use KI\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
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
            ->add('image', ImageSelector::class)
            ->add('mailEvent')
            ->add('mailModification')
            ->add('mailShotgun')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => User::class
        ));
    }

    public function getParent()
    {
        return RegistrationFormType::class;
    }
}
