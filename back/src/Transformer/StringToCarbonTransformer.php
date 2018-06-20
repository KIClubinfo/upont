<?php
namespace App\Transformer;

use App\Repository\ClubRepository;
use Carbon\Carbon;
use Symfony\Component\Form\DataTransformerInterface;


class StringToCarbonTransformer implements DataTransformerInterface
{
    public function transform($carbon)
    {
        /**
         * @var Carbon $carbon
         */
        if ($carbon === null) {
            return '';
        }

        return $carbon->toIso8601ZuluString();
    }

    public function reverseTransform($string)
    {
        if(empty($string)) {
            return null;
        }

        return Carbon::parse($string);
    }
}
