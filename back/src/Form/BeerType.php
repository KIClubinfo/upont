<?php

namespace App\Form;

use App\Selector\ImageSelector;
use App\Entity\Beer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BeerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('alcohol')
            ->add('volume')
            ->add('image', ImageSelector::class)
            ->add('active', CheckboxType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Beer::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
