<?php

namespace KI\PublicationBundle\Form;

use KI\PublicationBundle\Entity\Event;
use KI\UserBundle\Selector\ClubSelector;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('text')
            ->add('startDate')
            ->add('endDate')
            ->add('entryMethod')
            ->add('publicationState')
            ->add('shotgunDate')
            ->add('shotgunLimit')
            ->add('shotgunText')
            ->add('place')
            ->add('sendMail')
            ->add('authorClub', ClubSelector::class)
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
            'data_class' => Event::class,
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
