<?php
namespace App\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

use App\Entity\Tag;
use App\Repository\TagRepository;

class StringToTagsTransformer implements DataTransformerInterface
{
    protected $manager;
    protected $tagRepository;

    public function __construct(EntityManagerInterface $manager, TagRepository $tagRepository)
    {
        $this->manager    = $manager;
        $this->repository = $tagRepository;
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
