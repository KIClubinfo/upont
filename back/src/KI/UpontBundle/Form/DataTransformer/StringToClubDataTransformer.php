<?php
namespace KI\UpontBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Users\Club;


class StringToClubDataTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    // En théorie, ne sera jamais utilisé
    public function transform($club)
    {
        if (null === $club)
            return '';

        return $club->getSlug();
    }

    public function reverseTransform($string)
    {
        if (!$string)
            return null;

        $repo = $this->om->getRepository('KIUpontBundle:Users\Club');
        return $repo->findOneBySlug($string);
    }
}
