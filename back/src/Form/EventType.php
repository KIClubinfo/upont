<?php

namespace App\Form;

use App\Entity\Event;
use App\Selector\CarbonSelector;
use App\Selector\ClubSelector;
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
            ->add('startDate', CarbonSelector::class)
            ->add('endDate', CarbonSelector::class)
            ->add('entryMethod')
            ->add('shotgunDate', CarbonSelector::class)
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
