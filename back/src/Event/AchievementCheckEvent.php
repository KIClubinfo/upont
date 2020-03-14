<?php

namespace App\Event;

use App\Entity\Achievement;
use Symfony\Contracts\EventDispatcher\Event;

// Lance un check d'achievement avec l'id de l'achievement correspondant
// Et l'user qui tente d'obtenir l'achievement
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
