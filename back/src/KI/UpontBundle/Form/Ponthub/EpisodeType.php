<?php

namespace KI\UpontBundle\Form\Ponthub;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use KI\UpontBundle\Form\TagType;

class EpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Ponthub\Episode'
        ));
    }
        
    public function getName()
    {
        return '';
    }
}
