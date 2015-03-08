<?php

namespace KI\UpontBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use KI\UpontBundle\Entity\Core\Log;

class LogListener extends ContainerAware
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $session = $this->container->get('security.context')->getToken();
        if (!method_exists($session, 'getUser'))
            return;

        $manager = $this->container->get('doctrine')->getManager();
        $user = $session->getUser();
        $user->setLastConnect(time());
        $manager->flush();
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        $log = new Log();
        $log->setDate(time());
        $request = $event->getRequest();
        $response = $event->getResponse();

        $log->setMethod($request->getMethod());
        $log->setUrl(str_replace($request->getBaseUrl(), '', $request->getRequestUri()));
        $log->setParams(json_encode($request->request->all()));
        $log->setCode($response->getStatusCode());

        $session = $this->container->get('security.context')->getToken();
        $user = method_exists($session, 'getUser') ? $session->getUser()->getUsername() : '';
        $log->setUsername($user);
        $log->setIp($request->getClientIp());

        $agent = $request->headers->get('User-Agent');
        $log->setBrowser($this->browserUserAgent($agent));
        $log->setSystem($this->systemUserAgent($agent));
        $log->setAgent($agent);

        $manager = $this->container->get('doctrine')->getManager();

        // ATTENTION ! On ne veut pas flusher toutes les entités éventuellement
        // mal changées jusqu'ici, on les détache toutes pour être certain de ne
        // pas faire de connerie quand le kernel se termine.
        $manager->clear();
        $manager->persist($log);
        $manager->flush();
    }

    // Retourne le système pour un user agent donné
    // L'ORDRE DE VÉRIFICATION EST IMPORTANT!
    // Par exemple Android est aussi un système Linux... il ne faudrait pas confondre les deux
    public function systemUserAgent($agent)
    {
        $systems = array(
            'Linux'          => 'Linux',
            'Android'        => 'Android',
            'Windows NT 5.1' => 'Windows XP',
            'Windows NT 6.0' => 'Windows Vista',
            'Windows NT 6.1' => 'Windows 7',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.3' => 'Windows 8',
            'iPhone'         => 'iOS',
            'iPod'           => 'iOS',
            'iPad'           => 'iOS',
            'Intel Mac OS X' => 'MacOS X'
        );

        $system = 'Autre';
        foreach ($systems as $needle => $result)
            $system = preg_match('#' . $needle . '#isU', $agent) ? $result : $system;

        return $system;
    }

    // Retourne le navigateur internet pour un user agent donné
    public function browserUserAgent($agent)
    {
        $browsers = array(
            'Safari'  => 'Safari',
            'Chrome'  => 'Chrome',
            'Firefox' => 'Firefox',
            'Opera'   => 'Opera',
            'MSIE'    => 'Internet Explorer',
            'Trident' => 'Internet Explorer'
        );

        $browser = 'Autre';
        foreach ($browsers as $needle => $result)
            $browser = preg_match('#' . $needle . '#isU', $agent) ? $result : $browser;

        return $browser;
    }
}
