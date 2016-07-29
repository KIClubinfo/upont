<?php

namespace KI\PublicationBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            ->add('shotgunDate')
            ->add('shotgunLimit')
            ->add('shotgunText')
            ->add('place')
            ->add('sendMail')
            ->add('authorClub', 'KI\UserBundle\Selector\ClubSelector')
            ->add('uploadedFiles', 'Symfony\Component\Form\Extension\Core\Type\FileType', array(
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
            'data_class' => 'KI\PublicationBundle\Entity\Event',
            'csrf_protection' => false,
        ));
    }
}
