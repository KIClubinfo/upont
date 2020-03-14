<?php

namespace App\Service;

use App\Entity\User;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

class MailerService
{
    protected $mailer;
    protected $templating;

    public function __construct(Swift_Mailer $mailer, Environment $templating)
    {
        $this->mailer     = $mailer;
        $this->templating = $templating;
    }

    public function send(User $from, array $to, $title, $template, $vars, $attachments = [])
    {
        $message = (new Swift_Message($title))
            ->setFrom('evenements@upont.enpc.fr')
            ->setReplyTo([$from->getEmail() => $from->getFirstName().' '.$from->getLastName()])
        ;

        foreach($attachments as $attachment){
            $message->attach(Swift_Attachment::fromPath($attachment['path'])->setFilename($attachment['name']));
        }

        foreach ($to as $user) {
            $vars['username'] = $user->getUsername();
            $message
                ->setTo([$user->getEmail() => $user->getFirstName().' '.$user->getLastName()])
                ->setBody($this->templating->render($template, $vars), 'text/html')
            ;
            $this->mailer->send($message);
        }
    }
}
