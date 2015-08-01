<?php

namespace KI\PonthubBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoftwareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('genres', 'genres_selector')
            ->add('tags', 'tags_selector')
            ->add('year')
            ->add('version')
            ->add('author')
            ->add('image', 'image_selector');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\PonthubBundle\Entity\Software'
        ));
    }

    public function getName()
    {
        return '';
    }
}
