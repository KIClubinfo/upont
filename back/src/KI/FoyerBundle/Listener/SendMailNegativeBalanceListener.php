<?php

namespace KI\FoyerBundle\Listener;

use KI\FoyerBundle\Event\UserNegativeBalanceEvent;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class SendMailNegativeBalanceListener
{
    private $swiftMailer;
    private $templatingEngine;

    public function __construct(Swift_Mailer $swiftMailer, EngineInterface $templatingEngine)
    {
        $this->swiftMailer = $swiftMailer;
        $this->templatingEngine = $templatingEngine;
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function sendMail(UserNegativeBalanceEvent $event)
    {
        $user = $event->getUser();

        $message = (new Swift_Message('Pense à recharger ton compte foyer !'))
            ->setFrom('foyer.daube@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($this->templatingEngine->render('KIFoyerBundle::negative-balance.html.twig', [
                'user' => $user
            ]), 'text/html');

        $this->swiftMailer->send($message);
    }
}
