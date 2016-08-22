<?php

namespace KI\PonthubBundle\Form;

use KI\CoreBundle\Selector\ImageSelector;
use KI\CoreBundle\Selector\TagsSelector;
use KI\PonthubBundle\Entity\Movie;
use KI\PonthubBundle\Selector\ActorsSelector;
use KI\PonthubBundle\Selector\GenresSelector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
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
            'data_class' => Movie::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
