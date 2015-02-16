<?php

namespace KI\UpontBundle\Form\Ponthub;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use KI\UpontBundle\Form\TagType;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('genres', 'collection', array(
                'type' => new GenreType(),
                'allow_add' => true
            ))
            ->add('tags', 'collection', array(
                'type' => new TagType(),
                'allow_add' => true
            ))
            ->add('actors', 'collection', array(
                'type' => new ActorType(),
                'allow_add' => true
            ))
            ->add('duration')
            ->add('director')
            ->add('rating')
            ->add('year')
            ->add('vo')
            ->add('vf')
            ->add('vost')
            ->add('vostfr')
            ->add('hd');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
