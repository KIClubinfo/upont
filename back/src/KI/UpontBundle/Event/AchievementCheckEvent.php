<?php

namespace KI\UpontBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use KI\UpontBundle\Entity\Users\Achievement;

class AchievementCheckEvent extends Event
{
    protected $achievement;
    protected $user;

    public function __construct($id, $user = null)
    {
        $this->achievement = new Achievement($id);
        $this->user = $user;
    }

    public function getAchievement()
    {
        return $this->achievement;
    }

    public function getUser()
    {
        return $this->user;
    }
}
