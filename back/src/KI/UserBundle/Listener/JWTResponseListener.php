<?php

namespace KI\UserBundle\Listener;

use FOS\UserBundle\Doctrine\UserManager;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\AchievementCheckEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class JWTResponseListener
{
    protected $dispatcher;
    protected $userManager;

    public function __construct(EventDispatcherInterface $dispatcher, UserManager $userManager)
    {
        $this->dispatcher  = $dispatcher;
        $this->userManager = $userManager;
    }

    // Renvoi du token avec des informations supplémentaires
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        // On commence par checker éventuellement l'achievement de login
        $achievementCheck = new AchievementCheckEvent(Achievement::LOGIN);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $achievementCheck = new AchievementCheckEvent(Achievement::SPIRIT);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $achievementCheck = new AchievementCheckEvent(Achievement::KIEN);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $achievementCheck = new AchievementCheckEvent(Achievement::ADMIN);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);


        $balance = $user->getBalance();
        if ($balance !== null) {
            if ($balance < 0) {
                $achievementCheck = new AchievementCheckEvent(Achievement::FOYER);
                $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
            } else {
                $achievementCheck = new AchievementCheckEvent(Achievement::FOYER_BIS);
                $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
            }
        }

        $data['code'] = 200;
        $data['data'] = array(
            'username'   => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name'  => $user->getLastName(),
            'roles'      => $user->getRoles(),
            'first'      => $event->getRequest()->request->has('first')
        );

        $event->setData($data);
    }

    // Méthode custom quand un login est raté
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
    }
}
