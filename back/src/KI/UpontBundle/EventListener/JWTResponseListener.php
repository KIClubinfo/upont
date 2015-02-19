<?php

namespace KI\UpontBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use KI\UpontBundle\Entity\Achievement;
use KI\UpontBundle\Event\AchievementCheckEvent;

class JWTResponseListener
{
    private $container;
    private $em;

    public function __construct(ContainerInterface $container, EntityManager $manager)
    {
        $this->container = $container;
        $this->em = $manager;
    }

    // Renvoi du token avec des informations supplémentaires
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

        $data['code'] = 200;
        $data['data'] = array(
            'username' => $user->getUsername(),
            'roles'    => $user->getRoles(),
            'first'    => $event->getRequest()->request->has('first')
        );

        $event->setData($data);
    }

    protected function badCredentials($event)
    {
        return $event->setResponse(new JsonResponse(array(
            'code' => 401,
            'message' => 'Bad credentials'
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
            return $this->badCredentials($event);

        $username = $request->get('username');
        $password = $request->get('password');
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (!$user instanceof \KI\UpontBundle\Entity\Users\User)
            return $this->badCredentials($event);

        // On regarde si le mot de passe stocké dans la BDD est vide, si non on
        // balance une 401
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        if (!$encoder->isPasswordValid($user->getPassword(), '', $user->getSalt()))
            return $this->badCredentials($event);

        // Si le mot de passe de la BDD est vide, l'utilisateur se connecte pour
        // la première fois : on teste le mot de passe contre le proxy
        $proxyUrl = $this->container->getParameter('proxy_url');

        // Si pas de proxy configuré on affiche une erreur
        if ($proxyUrl === null)
            return $event->setResponse(new JsonResponse(array(
                'code' => 502,
                'message' => 'Proxy Error'
            ), 401));

        // Réglage des options cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com');
        curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . $password);

        // Récupération du HTTP CODE
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (in_array($code, array(0, 401, 403, 407)))
            return $this->badCredentials($event);

        // Si la connexion a réussie, le mot de passe proxy est bon
        // On le stocke dans la BDD (vol de mot de passe mwahahahah)
        $user->setPlainPassword($request->get('password'));
        $userManager->updateUser($user);

        // On reteste le login maintenant que le mot de passe est bon
        $curl = $this->container->get('ki_upont.curl');
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
