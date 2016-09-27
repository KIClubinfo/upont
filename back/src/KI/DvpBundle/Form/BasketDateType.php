<?php

namespace KI\DvpBundle\Form;

use KI\DvpBundle\Entity\BasketDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasketDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateRetrieve', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('locked')
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => BasketDate::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
