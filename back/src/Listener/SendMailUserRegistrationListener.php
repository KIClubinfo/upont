<?php

namespace App\Listener;

use App\Event\UserRegistrationEvent;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class SendMailUserRegistrationListener
{
    private $swiftMailer;
    private $templatingEngine;

    public function __construct(Swift_Mailer $swiftMailer, Environment $templatingEngine)
    {
        $this->swiftMailer = $swiftMailer;
        $this->templatingEngine = $templatingEngine;
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function sendMail(UserRegistrationEvent $event)
    {
        $attributes = $event->getAttributes();
        $email = $event->getUser()->getEmail();
        $username = $event->getUser()->getUsername();

        // Envoi du mail
        $message = (new Swift_Message('Inscription uPont'))
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo($email)
            ->setBody($this->templatingEngine->render('registration.txt.twig', $attributes));

        $this->swiftMailer->send($message);

        $message = (new Swift_Message('[uPont] Nouvelle inscription (' . $username . ')'))
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo('upont@clubinfo.enpc.fr')
            ->setBody($this->templatingEngine->render('registration-ki.txt.twig', $attributes));

        $this->swiftMailer->send($message);
    }
}
