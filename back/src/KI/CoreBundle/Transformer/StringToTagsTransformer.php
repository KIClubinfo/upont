<?php
namespace KI\CoreBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\CoreBundle\Entity\Tag;

class StringToTagsTransformer implements DataTransformerInterface
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
    public function transform($tags)
    {
        if (null === $tags)
            return '';

        return '';
    }

    public function reverseTransform($string)
    {
        if (!$string)
            return null;

        $array = new \Doctrine\Common\Collections\ArrayCollection();
        $repo = $this->om->getRepository('KICoreBundle:Tag');
        foreach (explode(',', $string) as $tag) {
            $item = $repo->findOneByName($tag);

            if ($item instanceof Tag) {
                $array->add($item);
            } else {
                $tagItem = new Tag();
                $tagItem->setName($tag);
                $this->om->persist($tagItem);
                $array->add($tagItem);
            }
        }
        $this->om->flush();

        return $array;
    }
}
