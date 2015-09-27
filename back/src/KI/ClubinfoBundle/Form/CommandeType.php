<?php

namespace KI\ClubinfoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('qunatity')
            ->add('centrale')
            ->add('user')
            ->add('taken')
            ->add('payed')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'KI\ClubinfoBundle\Entity\Commande',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}
