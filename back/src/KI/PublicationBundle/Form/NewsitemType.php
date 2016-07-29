<?php

namespace KI\PublicationBundle\Form;

use Symfony\Component\Form\AbstractType;
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
            ->add('authorClub', 'KI\UserBundle\Selector\ClubSelector')
            ->add('image', 'KI\CoreBundle\Selector\ImageSelector')
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
            'data_class' => 'KI\PublicationBundle\Entity\Newsitem',
            'csrf_protection' => false,
        ));
    }
}
