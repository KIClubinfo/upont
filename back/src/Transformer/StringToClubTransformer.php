<?php
namespace App\Transformer;

use App\Repository\ClubRepository;
use Symfony\Component\Form\DataTransformerInterface;


class StringToClubTransformer implements DataTransformerInterface
{
    /**
     * @var ClubRepository
     */
    private $clubRepository;

    public function __construct(ClubRepository $clubRepository)
    {
        $this->clubRepository = $clubRepository;
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

        return $this->clubRepository->findOneBySlug($string);
    }
}
