<?php

namespace KI\UserBundle\Listener;

use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\UserRegistrationEvent;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;

class SendMailUserRegistrationListener
{
    private $swiftMailer;
    private $twigEngine;

    public function __construct(Swift_Mailer $swiftMailer, TwigEngine $twigEngine)
    {
        $this->swiftMailer = $swiftMailer;
        $this->twigEngine = $twigEngine;
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function sendMail(UserRegistrationEvent $event)
    {
        $attributes = $event->getAttributes();
        $email = $event->getUser()->getEmail();
        $username = $event->getUser()->getUsername();

        // Envoi du mail
        $message = Swift_Message::newInstance()
            ->setSubject('Inscription uPont')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo($email)
            ->setBody($this->twigEngine->render('KIUserBundle::registration.txt.twig', $attributes));

        $this->swiftMailer->send($message);

        $message = Swift_Message::newInstance()
            ->setSubject('[uPont] Nouvelle inscription (' . $username . ')')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo('upont@clubinfo.enpc.fr')
            ->setBody($this->twigEngine->render('KIUserBundle::registration-ki.txt.twig', $attributes));

        $this->swiftMailer->send($message);
    }
}
