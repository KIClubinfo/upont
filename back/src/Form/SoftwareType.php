<?php

namespace App\Form;

use App\Selector\ImageSelector;
use App\Selector\TagsSelector;
use App\Entity\Software;
use App\Selector\GenresSelector;
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
            ->add('genres', GenresSelector::class)
            ->add('tags', TagsSelector::class)
            ->add('year')
            ->add('version')
            ->add('author')
            ->add('os')
            ->add('image', ImageSelector::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => Software::class
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
