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
        $shotgunConfig = [
            '2016' => [
                'simple' => 10,
                'double' => 20,
                'binom' => 20,
            ],
        ];

        $room = $admissible->getRoom();
        $success = false;
        if ($room == "simple" || $room == "double" || $room == "binome") {
            // On charge tous les admissibles de la série qui ont réussi le shotgun
            $admissibles = $this->admissibleRepository->createQueryBuilder('a')
                ->select('a.scei')
                ->where('a.year = :year')
                ->setParameter('year', strftime('%Y'))
                ->andWhere('a.serie = :serie')
                ->setParameter('serie', $admissible->getSerie())
                ->andWhere('a.room = :room')
                ->setParameter('room', $room)
                ->orderBy('a.date', 'ASC')
                ->getQuery()
                ->getResult();

            $success = array_search($admissible->getScei(), $admissibles) < $shotgunConfig[strftime('%Y')][$room];
        }

        $vars = [
            'admissible' => $admissible,
            'success' => $success,
        ];

        $this->mailerService->sendAdmissible($admissible,
            'Résultat du shotgun pour la résidence Meunier',
            'KIUserBundle::shotgun-admissible.html.twig',
            $vars
        );
    }
}
