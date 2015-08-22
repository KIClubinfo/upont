<?php

namespace KI\UserBundle\Listener;

use FOS\UserBundle\Doctrine\UserManager;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\AchievementCheckEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        // On commence par checker éventuellement l'achievement de login
        $achievementCheck = new AchievementCheckEvent(Achievement::LOGIN);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $achievementCheck = new AchievementCheckEvent(Achievement::SPIRIT);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $achievementCheck = new AchievementCheckEvent(Achievement::KIEN);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $achievementCheck = new AchievementCheckEvent(Achievement::ADMIN);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        if (!$user instanceof User) {
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

    /**
     * @param string $reason
     */
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
        $user = $this->userManager->findUserByUsername($username);

        if (!$user instanceof User)
            return $this->badCredentials($event, 'Utilisateur non trouvé');
        return $this->badCredentials($event, 'Mauvais mot de passe');
    }
}
