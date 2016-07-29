<?php

namespace KI\FoyerBundle\Helper;

use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\User;

class StatisticsHelper
{
    protected $beerRepository;
    protected $transactionRepository;
    protected $userRepository;

    public function __construct(EntityRepository $beerRepository,
                                EntityRepository $transactionRepository,
                                EntityRepository $userRepository)
    {
        $this->beerRepository     = $beerRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository     = $userRepository;
    }

    /**
     * Retourne les statistiques d'un utilisateur particulier
     * @param  User $user
     * @return array
     */
    public function getUserStatistics(User $user)
    {
        $timeGraph = [];
        $pieGraph = [];
        $beersCountArray = [];
        $volume = 0;
        $beerCount = 0;

        $transactions = $this->transactionRepository->findBy(['user' => $user], ['date' => 'ASC']);

        foreach ($transactions as $transaction) {
            $beer = $transaction->getBeer();
            // Compte crédité, pas une conso
            if ($beer === null) {
                continue;
            }
            $volume += $beer->getVolume();

            $timeGraph[$transaction->getDate()] = $volume;

            $beerCount++;

            if (!isset($pieGraph[$beer->getSlug()])) {
                $pieGraph[$beer->getSlug()] = ['beer' => $beer, 'count' => 0];
                $beersCountArray[$beer->getSlug()] = 0;
            }
            $pieGraph[$beer->getSlug()]['count']++;
            $beersCountArray[$beer->getSlug()]++;
        }

        array_multisort($beersCountArray, SORT_DESC, $pieGraph);

        return [
            'beersDrunk'    => $pieGraph,
            'stackedLiters' => $timeGraph,
            'totalLiters'   => $volume,
            'totalBeers'    => $beerCount
        ];
    }
}
