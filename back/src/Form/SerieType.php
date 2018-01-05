<?php

namespace App\Form;

use App\Selector\ImageSelector;
use App\Selector\TagsSelector;
use App\Entity\Serie;
use App\Selector\ActorsSelector;
use App\Selector\GenresSelector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SerieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('actors', ActorsSelector::class)
            ->add('genres', GenresSelector::class)
            ->add('tags', TagsSelector::class)
            ->add('duration')
            ->add('director')
            ->add('rating')
            ->add('year')
            ->add('image', ImageSelector::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Serie::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
