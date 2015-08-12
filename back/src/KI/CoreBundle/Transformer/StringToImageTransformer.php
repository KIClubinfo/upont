<?php
namespace KI\CoreBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;
use KI\CoreBundle\Service\ImageService;

class StringToImageTransformer implements DataTransformerInterface
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function reverseTransform($base64orUrl)
    {
        if (!$base64orUrl) {
            return null;
        }

        return $this->imageService->upload($base64orUrl);
    }
}
