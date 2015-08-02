<?php
namespace KI\PonthubBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\PonthubBundle\Entity\Genre;

class StringToGenresTransformer implements DataTransformerInterface
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
    public function transform($genres)
    {
        if (null === $genres)
            return '';

        return '';
    }

    public function reverseTransform($string)
    {
        if (!$string)
            return null;

        $array = new \Doctrine\Common\Collections\ArrayCollection();
        $repo = $this->om->getRepository('KIPonthubBundle:Genre');
        foreach (explode(',', $string) as $genre) {
            $item = $repo->findOneByName($genre);

            if ($item instanceof Genre) {
                $array->add($item);
            } else {
                $genreItem = new Genre();
                $genreItem->setName($genre);
                $this->om->persist($genreItem);
                $array->add($genreItem);
            }
        }
        $this->om->flush();

        return $array;
    }
}
