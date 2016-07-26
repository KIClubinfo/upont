<?php

namespace KI\UserBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;
use KI\UserBundle\Entity\Achievement;

class UserRegistrationEvent extends Event
{
    protected $user;
    protected $attributes;

    public function __construct(UserInterface $user, array $attributes)
    {
        $this->user = $user;
        $this->attributes = $attributes;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getUser()
    {
        return $this->user;
    }
}
