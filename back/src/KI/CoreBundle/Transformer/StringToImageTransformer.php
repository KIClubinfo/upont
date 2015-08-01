<?php
namespace KI\CoreBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use KI\CoreBundle\Services\ImageService;

class StringToImageTransformer implements DataTransformerInterface
{
    private $uploaderService;

    public function __construct(ObjectManager $om, ImageService $uploaderService)
    {
        $this->om = $om;
        $this->uploaderService = $uploaderService;
    }

    /**
     * En théorie, ne doit pas être utilisé.
     *
     * @param  $img
     * @return string
     */
    public function transform($image)
    {
        if (null === $image)
            return '';

        return $image->getWebPath();
    }

    /**
     * Transforms an url/base64 to an Image.
     *
     * @param  string $base64orUrl
     * @return Image|null
     * @throws TransformationFailedException if Image is not good/uploaded.
     */
    public function reverseTransform($base64orUrl)
    {
        if (!$base64orUrl)
            return null;
        return $this->uploaderService->upload($base64orUrl);
    }
}
