<?php
namespace KI\UpontBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use KI\UpontBundle\Form\DataTransformer\Base64OrUrlToImageDataTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use KI\UpontBundle\Services\KIImages;

class ImageUploaderSelectorType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    private $uploaderService;


    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, KIImages $uploaderService)
    {
        $this->om = $om;
        $this->uploaderService = $uploaderService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new Base64OrUrlToImageDataTransformer($this->om, $this->uploaderService);
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'L image selectionnee n existe pas',
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'imageuploader_selector';
    }
}

