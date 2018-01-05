<?php
namespace App\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use App\Entity\Tag;
use Symfony\Component\Form\DataTransformerInterface;

class StringToTagsTransformer implements DataTransformerInterface
{
    protected $manager;
    protected $repository;

    public function __construct(EntityManager $manager, EntityRepository $repository)
    {
        $this->manager    = $manager;
        $this->repository = $repository;
    }

    // En théorie n'est jamais utilisé
    public function transform($image) { return; }

    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        $array = new ArrayCollection();

        foreach (explode(',', $string) as $tag) {
            $item = $this->repository->findOneByName($tag);

            if ($item instanceof Tag) {
                $array->add($item);
            } else {
                $tagItem = new Tag();
                $tagItem->setName($tag);
                $this->manager->persist($tagItem);
                $array->add($tagItem);
            }
        }
        $this->manager->flush();

        return $array;
    }
}
