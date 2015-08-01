<?php

namespace KI\PonthubBundle\Service\Gracenote;

// Extend normal PHP exceptions by includes an additional information field we can utilize.
class GNException extends \Exception
{
    private $extInfo; // Additional information on the exception.

    public function __construct($code = 0, $extInfo = '')
    {
        parent::__construct(GNError::getMessage($code), $code);
        $this->extInfo = $extInfo;
    }

    public function getExtraInfo() { return $this->extInfo; }
}
