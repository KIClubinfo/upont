<?php

namespace App\Form;

use App\Selector\ImageSelector;
use App\Entity\Newsitem;
use App\Selector\ClubSelector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsitemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('text')
            ->add('sendMail')
            ->add('authorClub', ClubSelector::class)
            ->add('image', ImageSelector::class)
            ->add('uploadedFiles', FileType::class, [
                    'multiple' => true,
                    'data_class' => null,
                    'required' => false,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Newsitem::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
