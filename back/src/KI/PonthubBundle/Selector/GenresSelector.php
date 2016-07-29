<?php
namespace KI\PonthubBundle\Selector;

use KI\PonthubBundle\Transformer\StringToGenresTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GenresSelector extends AbstractType
{
    protected $transformer;

    public function __construct(StringToGenresTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }
}
