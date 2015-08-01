<?php

namespace KI\UpontBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\UpontBundle\Entity\Users\Achievement;
use KI\UpontBundle\Event\AchievementCheckEvent;

class JWTResponseListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    // Renvoi du token avec des informations supplémentaires
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        // On commence par checker éventuellement l'achievement de login
        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::LOGIN);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::SPIRIT);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::KIEN);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::ADMIN);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User) {
            return;
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

    protected function badCredentials(AuthenticationFailureEvent $event, $reason)
    {
        return $event->setResponse(new JsonResponse(array(
            'code' => 401,
            'message' => 'Bad credentials',
            'reason' => $reason
        ), 401));
    }

    // Méthode custom pour gérer le fait qu'un utilisateur avec mot de passe vide
    // se connecte pour la première fois : on teste donc le mdp contre le proxy
    // de la DSI
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        ob_start();
        $request = $event->getRequest()->request;

        if (!($request->has('username')
            && $request->has('password')
            && $request->get('username') != ''
            && $request->get('password') != ''))
            return $this->badCredentials($event, 'Champs non remplis');

        $username = $request->get('username');
        $password = $request->get('password');
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User)
            return $this->badCredentials($event, 'Utilisateur non trouvé');
        return $this->badCredentials($event, 'Mauvais mot de passe');
    }
}
