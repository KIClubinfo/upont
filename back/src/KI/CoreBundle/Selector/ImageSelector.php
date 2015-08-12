<?php
namespace KI\CoreBundle\Selector;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use KI\CoreBundle\Transformer\StringToImageTransformer;

class ImageSelector extends AbstractType
{
    protected $transformer;

    public function __construct(StringToImageTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'L\'image selectionnee n existe pas',
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'image_selector';
    }
}
