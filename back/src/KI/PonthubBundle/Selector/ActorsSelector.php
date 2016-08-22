<?php
namespace KI\PonthubBundle\Selector;

use KI\PonthubBundle\Transformer\StringToActorsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ActorsSelector extends AbstractType
{
    protected $transformer;

    public function __construct(StringToActorsTransformer $transformer)
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
