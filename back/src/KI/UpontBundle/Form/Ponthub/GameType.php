<?php

namespace KI\UpontBundle\Form\Ponthub;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use KI\UpontBundle\Form\TagType;

class GameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('genres', 'genres_selector')
            ->add('tags', 'tags_selector')
            ->add('year')
            ->add('studio')
            ->add('image', 'imageuploader_selector');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Ponthub\Game'
        ));
    }

    public function getName()
    {
        return '';
    }
}
