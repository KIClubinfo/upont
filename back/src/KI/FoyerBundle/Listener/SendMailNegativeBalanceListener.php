<?php

namespace KI\FoyerBundle\Listener;

use KI\FoyerBundle\Event\UserNegativeBalanceEvent;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;

class SendMailNegativeBalanceListener
{
    private $swiftMailer;
    private $twigEngine;

    public function __construct(Swift_Mailer $swiftMailer, TwigEngine $twigEngine)
    {
        $this->swiftMailer = $swiftMailer;
        $this->twigEngine = $twigEngine;
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function sendMail(UserNegativeBalanceEvent $event)
    {
        $user = $event->getUser();

        $message = Swift_Message::newInstance()
            ->setSubject('Pense à recharger ton compte foyer !')
            ->setFrom('foyer.daube@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($this->twigEngine->render('KIFoyerBundle::negative-balance.html.twig', [
                'user' => $user
            ]), 'text/html');

        $this->swiftMailer->send($message);
    }
}
