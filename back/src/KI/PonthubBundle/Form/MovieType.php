<?php

namespace KI\UpontBundle\Form\Ponthub;

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
            ->add('actors', 'actors_selector')
            ->add('genres', 'genres_selector')
            ->add('tags', 'tags_selector')
            ->add('duration')
            ->add('director')
            ->add('rating')
            ->add('year')
            ->add('vo')
            ->add('vf')
            ->add('vost')
            ->add('vostfr')
            ->add('hd')
            ->add('image', 'imageuploader_selector');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Ponthub\Movie'
        ));
    }

    public function getName()
    {
        return '';
    }
}
