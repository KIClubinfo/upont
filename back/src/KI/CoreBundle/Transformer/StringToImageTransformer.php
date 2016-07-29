<?php
namespace KI\CoreBundle\Transformer;

use KI\CoreBundle\Service\ImageService;
use Symfony\Component\Form\DataTransformerInterface;

class StringToImageTransformer implements DataTransformerInterface
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    // En théorie n'est jamais utilisé
    public function transform($image) { return; }

    public function reverseTransform($base64orUrl)
    {
        if (!$base64orUrl) {
            return null;
        }

        return $this->imageService->upload($base64orUrl);
    }
}
