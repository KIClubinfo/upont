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
     * Retourne les statistiques générales
     * @return array
     */
    public function getMainStatistics()
    {
        return array(
            'hallOfFame' => $this->getHallOfFame()
        );
    }

    /**
     * Retourne une liste des meilleurs buveurs (avec solde positif !)
     * @return array($position => array(User $user, float $liters),...)
     */
    private function getHallOfFame()
    {
        $return = $users = $liters = array();

        $transactions = $this->transactionRepository->findAll();
        foreach ($transactions as $transaction) {
            $user = $transaction->getUser();
            $beer = $transaction->getBeer();
            $slug = $user->getSlug();

            // Compte crédité, pas une conso
            if ($beer === null) {
                continue;
            }

            if (!isset($users[$slug])) {
                $users[$slug] = array('user' => $user, 'liters' => 0);
            }
            $users[$slug]['liters'] += $beer->getVolume();
        }

        foreach ($users as $user) {
            $return[] = $user;
            $liters[] = $user['liters'];
        }
        array_multisort($liters, SORT_DESC, $return);
        return array_slice($return, 0, 10);
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

        $transactions = $this->transactionRepository->findBy(["user" => $user],["date" => "ASC"]);

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
                $pieGraph[$beer->getSlug()] = array('beer' => $beer, 'count' => 0);
                $beersCountArray[$beer->getSlug()] = 0;
            }
            $pieGraph[$beer->getSlug()]['count']++;
            $beersCountArray[$beer->getSlug()]++;
        }

        array_multisort($beersCountArray, SORT_DESC, $pieGraph);

        return array(
            'beersDrunk'    => $pieGraph,
            'stackedLiters' => $timeGraph,
            'totalLiters'   => $volume,
            'totalBeers'    => $beerCount
        );
    }
}
