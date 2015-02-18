<?php

namespace KI\UpontBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\UpontBundle\Entity\Achievement;
use KI\UpontBundle\Event\AchievementCheckEvent;

class JWTResponseListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Add public data to the authentication response
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        // On commence par cehcker éventuellement l'achievement de login
        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::LOGIN);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User) {
            return;
        }

        $data['data'] = array(
            'username' => $user->getUsername(),
            'roles'    => $user->getRoles()
        );

        $event->setData($data);
    }
}
