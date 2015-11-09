<?php
namespace KI\UserBundle\Selector;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use KI\UserBundle\Transformer\StringToClubTransformer;

class ClubSelector extends AbstractType
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new StringToClubTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'Le club sélectionné n\'existe pas',
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
