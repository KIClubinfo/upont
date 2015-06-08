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
        // On commence par checker éventuellement l'achievement de login
        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::LOGIN);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User) {
            return;
        }

        $data['code'] = 200;
        $data['data'] = array(
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'roles'    => $user->getRoles(),
            'first'    => $event->getRequest()->request->has('first')
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

        // On regarde si l'utilisateur est activé ou non, si oui on balance une 401
        if ($user->isEnabled())
            return $this->badCredentials($event, 'Mauvais mot de passe');

        // Si le mot de passe de la BDD est vide, l'utilisateur se connecte pour
        // la première, on teste contre la v1
        $curl = $this->container->get('ki_upont.curl');
        $data = $curl->curl('https://upont.enpc.fr/v1/api.php?action=login_v1&username='.$username.'&password='.$password, array(
            CURLOPT_PROXY => ''
        ));
        if (!preg_match('#true#', $data))
            return $this->badCredentials($event, 'Mauvais mot de passe v1');

        // Si la connexion a réussi, le mot de passe proxy est bon
        // On le stocke dans la BDD (vol de mot de passe mwahahahah)
        $user->setPlainPassword($password);
        $user->setEnabled(true);
        $userManager->updateUser($user);

        // On reteste le login maintenant que le mot de passe est bon
        $data = $curl->curl($event->getRequest()->getUri(), array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => array('username' => $username, 'password' => $password, 'first' => 1),
            CURLOPT_PROXY => ''
        ));
        $data = json_decode($data, true);
        ob_end_clean();
        return $event->setResponse(new JsonResponse($data, $data['code']));
    }
}
