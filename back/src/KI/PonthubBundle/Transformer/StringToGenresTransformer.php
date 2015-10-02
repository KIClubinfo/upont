<?php
namespace KI\PonthubBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\PonthubBundle\Entity\Genre;

class StringToGenresTransformer implements DataTransformerInterface
{
    protected $manager;
    protected $genreRepository;

    public function __construct(EntityManager $manager, EntityRepository $genreRepository)
    {
        $this->manager         = $manager;
        $this->genreRepository = $genreRepository;
    }

    // En théorie, ne sera jamais utilisé
    public function transform($genres)
    {
        return '';
    }

    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        $array = new \Doctrine\Common\Collections\ArrayCollection();
        foreach (explode(',', $string) as $genre) {
            $item = $this->genreRepository->findOneByName($genre);

            if ($item instanceof Genre) {
                $array->add($item);
            } else {
                $genreItem = new Genre();
                $genreItem->setName($genre);
                $this->manager->persist($genreItem);
                $array->add($genreItem);
            }
        }
        $this->manager->flush();

        return $array;
    }
}
