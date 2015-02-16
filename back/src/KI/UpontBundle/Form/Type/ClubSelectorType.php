<?php
namespace KI\UpontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use KI\UpontBundle\Form\DataTransformer\Base64OrUrlToImageDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use KI\UpontBundle\Form\DataTransformer\StringToClubDataTransformer;

class ClubSelectorType extends AbstractType
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new StringToClubDataTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'Le club séléctionné n\'existe pas',
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'club_selector';
    }
}
