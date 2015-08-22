<?php

namespace KI\PublicationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('groups', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
                'allow_delete' => true
            ))
            ->add('department')
            ->add('semester')
            ->add('ects')
            ->add('active')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'KI\PublicationBundle\Entity\Course',
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return '';
    }
}
