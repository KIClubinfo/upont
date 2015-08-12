<?php
namespace KI\CoreBundle\Selector;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use KI\CoreBundle\Transformer\StringToTagsTransformer;

class TagsSelector extends AbstractType
{
    protected $transformer;

    public function __construct(StringToTagsTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'tags_selector';
    }
}
