<?php
namespace KI\CoreBundle\Form\Selector;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolver;
use KI\CoreBundle\Transformer\StringToImageTransformer;
use KI\CoreBundle\Service\ImageService;

class ImageSelectorType extends AbstractType
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
