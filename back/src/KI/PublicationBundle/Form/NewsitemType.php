<?php

namespace KI\PublicationBundle\Form;

use KI\CoreBundle\Selector\ImageSelector;
use KI\PublicationBundle\Entity\Newsitem;
use KI\UserBundle\Selector\ClubSelector;
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
            ->add('uploadedFiles', FileType::class, array(
                    'multiple' => true,
                    'data_class' => null,
                    'required' => false,
                )
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Newsitem::class,
            'csrf_protection' => false,
        ));
    }
}
