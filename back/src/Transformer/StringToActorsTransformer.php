<?php
namespace App\Transformer;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Actor;
use App\Repository\ActorRepository;
use Symfony\Component\Form\DataTransformerInterface;

class StringToActorsTransformer implements DataTransformerInterface
{
    protected $manager;
    protected $actorRepository;

    public function __construct(EntityManagerInterface $manager, ActorRepository $actorRepository)
    {
        $this->manager         = $manager;
        $this->actorRepository = $actorRepository;
    }

    // En théorie, ne sera jamais utilisé
    public function transform($actors)
    {
        return '';
    }

    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        $array = new \Doctrine\Common\Collections\ArrayCollection();
        foreach (explode(',', $string) as $actor) {
            $item = $this->actorRepository->findOneByName($actor);

            if ($item instanceof Actor) {
                $array->add($item);
            } else {
                $actorItem = new Actor();
                $actorItem->setName($actor);
                $this->manager->persist($actorItem);
                $array->add($actorItem);
            }
        }
        $this->manager->flush();

        return $array;
    }
}
