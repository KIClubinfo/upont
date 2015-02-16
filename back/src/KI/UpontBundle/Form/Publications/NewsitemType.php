<?php

namespace KI\UpontBundle\Form\Publications;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsitemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('textShort')
            ->add('textLong')
            ->add('authorClub', 'club_selector')
            ->add('image', 'imageuploader_selector');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => 'KI\UpontBundle\Entity\Publications\Newsitem'
        ));
    }

    public function getName()
    {
        return '';
    }
}
