<?php

namespace KI\UpontBundle\Form\Publications;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('authorClub', 'club_selector')
            ->add('image', 'imageuploader_selector');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Publications\Event'
        ));
    }

    public function getName()
    {
        return '';
    }
}
