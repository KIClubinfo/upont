<?php
namespace App\Selector;

use App\Repository\ClubRepository;
use App\Transformer\StringToClubTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClubSelector extends AbstractType
{
    private $clubRepository;

    public function __construct(ClubRepository $clubRepository)
    {
        $this->clubRepository = $clubRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new StringToClubTransformer($this->clubRepository);
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
