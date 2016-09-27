<?php

namespace KI\PublicationBundle\Listener;

use Doctrine\ORM\EntityRepository;
use KI\PublicationBundle\Entity\Newsitem;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\Club;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\UserBundle\Service\MailerService;
use KI\UserBundle\Service\NotifyService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NewsitemListener
{
    protected $dispatcher;
    protected $mailerService;
    protected $notifyService;
    protected $userRepository;

    public function __construct(EventDispatcherInterface $dispatcher,
                                MailerService $mailerService,
                                NotifyService $notifyService,
                                EntityRepository $userRepository)
    {
        $this->dispatcher     = $dispatcher;
        $this->mailerService  = $mailerService;
        $this->notifyService  = $notifyService;
        $this->userRepository = $userRepository;
    }

    public function postPersist(Newsitem $newsitem)
    {
        $club = $newsitem->getAuthorClub();
        $text = substr($newsitem->getText(), 0, 140).'...';

        // Si ce n'est pas un message perso, on notifie les utilisateurs suivant le club
        if ($club) {
            $achievementCheck = new AchievementCheckEvent(Achievement::NEWS_CREATE);
            $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

            list($usersPush, $usersMail) = $this->getUsersToNotify($club, $newsitem->getSendMail());

            $vars = ['post' => $newsitem];

            $attachments = [];
            foreach ($newsitem->getFiles() as $file) {
                $attachments[] = ['path' => $file->getAbsolutePath(), 'name' => $file->getName()];
            }

            $title = '['.$club->getName().']'.' '.$newsitem->getName();
            $this->mailerService->send($newsitem->getAuthorUser(),
                $usersMail,
                $title,
                'KIPublicationBundle::news.html.twig',
                $vars,
                $attachments
            );

            $text = substr($newsitem->getText(), 0, 140).'...';
            $this->notifyService->notify(
                'notif_followed_news',
                $newsitem->getName(),
                $text,
                'to',
                $usersPush
            );
        } else {
            // Si c'est une news perso on notifie tous ceux qui ont envie
            $this->notifyService->notify(
                'notif_news_perso',
                $newsitem->getName(),
                $text,
                'exclude',
                []
            );
        }
    }

    /**
     * Retourne les utilisateurs que l'on peut notifier par Push et/ou Mail
     * @param  Club  $club   Le club servant à déterminer l'égilibilité
     * @return array
     */
    private function getUsersToNotify(Club $club, $sendMail = false)
    {
        $allUsers = $this->userRepository->findAll();
        $usersPush = $usersMail = [];

        foreach ($allUsers as $candidate) {
            if (!$candidate->getClubsNotFollowed()->contains($club)) {
                $usersPush[] = $candidate;

                if ($sendMail && $candidate->getMailEvent()) {
                    $usersMail[] = $candidate;
                }
            }
        }
        return [$usersPush, $usersMail];
    }
}
