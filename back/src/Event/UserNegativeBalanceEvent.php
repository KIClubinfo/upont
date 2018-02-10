<?php

namespace App\Event;

use FOS\UserBundle\Model\UserInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserNegativeBalanceEvent extends Event
{
    protected $user;
    protected $firstTime;

    public function __construct(UserInterface $user, $firstTime)
    {
        $this->user = $user;
        $this->firstTime = $firstTime;
    }

    public function isFirstTime()
    {
        return $this->firstTime;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
