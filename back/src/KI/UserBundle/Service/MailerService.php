<?php

namespace KI\UserBundle\Service;

use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;

class MailerService
{
    protected $mailer;
    protected $templating;

    public function __construct(Swift_Mailer $mailer, TwigEngine $templating)
    {
        $this->mailer     = $mailer;
        $this->templating = $templating;
    }

    public function send($to, $title, $template, $vars)
    {
        $message = Swift_Message::newInstance()
            ->setSubject($title)
            ->setFrom('noreply@upont.enpc.fr')
            ->setBody($this->templating->render($template, $vars), 'text/html')
        ;

        foreach ($to as $user) {
            $message->setTo(array($user->getEmail() => $user->getFirstName().' '.$user->getLastName()));
            $this->mailer->send($message);
        }
    }
}
