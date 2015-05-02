<?php

namespace KI\UpontBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use KI\UpontBundle\Entity\Users\Achievement;
 
class AchievementCheckEvent extends Event
{
    protected $achievement;
 
    public function __construct($id)
    {
        $this->achievement = new Achievement($id);
    }
 
    public function getAchievement()
    {
        return $this->achievement;
    }
}
