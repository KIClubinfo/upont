<?php

namespace App\Listener;

use App\Entity\Achievement;
use App\Entity\Club;
use App\Entity\Newsitem;
use App\Event\AchievementCheckEvent;
use App\Repository\UserRepository;
use App\Service\MailerService;
use App\Service\NotifyService;
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
                                UserRepository $userRepository)
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
            $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::NEWS_CREATE));

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
                'news.html.twig',
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
