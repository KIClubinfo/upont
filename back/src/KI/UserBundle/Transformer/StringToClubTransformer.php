<?php
namespace KI\UserBundle\Transformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;


class StringToClubTransformer implements DataTransformerInterface
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

        $repo = $this->om->getRepository('KIUserBundle:Club');
        return $repo->findOneBySlug($string);
    }
}
