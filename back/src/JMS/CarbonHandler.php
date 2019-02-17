<?php

namespace App\JMS;

use Carbon\Carbon;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;

class CarbonHandler implements SubscribingHandlerInterface
{
    /**
     * @return array
     */
    public static function getSubscribingMethods()
    {
        $methods = [
            [
                'type' => Carbon::class,
                'format' => 'json',
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'method' => 'deserializeCarbon'
            ],
            [
                'type' => Carbon::class,
                'format' => 'json',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'method' => 'serializeCarbon'
            ],
        ];

        return $methods;
    }

    /**
     * @param VisitorInterface $visitor
     * @param Carbon $date
     * @param array $type
     * @param Context $context
     *
     * @return string
     */
    public function serializeCarbon(VisitorInterface $visitor, Carbon $date, array $type, Context $context)
    {
        return $visitor->visitString($date->jsonSerialize(), $type, $context);
    }

    /**
     * @param VisitorInterface $visitor
     * @param string $data
     * @param array $type
     *
     * @return Carbon|null
     */
    public function deserializeCarbon(VisitorInterface $visitor, $data, array $type)
    {
        if ($data === null) {
            return null;
        }

        return Carbon::parse($data);
    }
}