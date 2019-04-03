<?php

namespace App\Listener;

use Gedmo\Sluggable\SluggableListener as GedmoSluggableListener;
use Gedmo\Sluggable\Util\Urlizer;
use ReflectionClass;

class SluggableListener extends GedmoSluggableListener
{

    public function __construct()
    {
        parent::__construct();
        $this->setTransliterator([self::class, 'transliterate']);
    }

    public static function transliterate($text, $separator, $object)
    {
        // Urlizer is what Sluggable uses to generate the slug.
        $generatedSlug = Urlizer::transliterate($text, $separator);

        // If generated slug is empty, try to use the short class name.
        if ($generatedSlug === '') {
            $className = (new ReflectionClass($object))->getShortName();
            // Regexp to convert 'ClassName' to 'Class Name' based on http://stackoverflow.com/a/19533226/1813625.
            // It does not cover all possibilities perfectly, but I don't care that much.
            $generatedSlug = preg_replace('/(?<!^)[A-Z]+/', ' $0', $className);
            $generatedSlug = Urlizer::transliterate($generatedSlug, $separator);
        }

        // At this point the slug should never be empty at all, but just in case...
        if ($generatedSlug === '') {
            $generatedSlug = 's';
        }

        // The returned slug will still be made unique by Sluggable after this point if specified!
        return $generatedSlug;
    }
}