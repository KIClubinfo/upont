<?php

namespace App\Event;

use FOS\UserBundle\Model\UserInterface;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

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

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
