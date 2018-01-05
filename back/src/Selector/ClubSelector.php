<?php
namespace App\Selector;

use Doctrine\Common\Persistence\ObjectManager;
use App\Transformer\StringToClubTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $resolver->setDefaults([
            'invalid_message' => 'Le club séléctionné n\'existe pas',
        ]);
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }
}
