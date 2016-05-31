<?php

namespace KI\UserBundle\Listener;

use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\Admissible;
use KI\UserBundle\Service\MailerService;

class AdmissibleListener
{
    protected $mailerService;
    protected $userRepository;
    protected $admissibleRepository;

    public function __construct(MailerService $mailerService,
                                EntityRepository $userRepository,
                                EntityRepository $admissibleRepository)
    {
        $this->mailerService = $mailerService;
        $this->userRepository = $userRepository;
        $this->admissibleRepository = $admissibleRepository;
    }

    public function postPersist(Admissible $admissible)
    {
        $vars = [
            'admissible' => $admissible,
        ];

        $this->mailerService->sendAdmissible($admissible,
            '[CCMP2016] Demande de logement à la résidence Meunier',
            'KIUserBundle::shotgun-admissible.html.twig',
            $vars
        );
    }
}
