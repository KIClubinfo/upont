<?php

namespace KI\FoyerBundle\Helper;

use Doctrine\ORM\EntityRepository;
use KI\UserBundle\Entity\User;

class StatisticsHelper
{
    protected $beerRepository;
    protected $beerUserRepository;
    protected $userRepository;

    public function __construct(EntityRepository $beerRepository,
                                EntityRepository $beerUserRepository,
                                EntityRepository $userRepository)
    {
        $this->beerRepository     = $beerRepository;
        $this->beerUserRepository = $beerUserRepository;
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

        $beerUsers = $this->beerUserRepository->findAll();
        foreach ($beerUsers as $beerUser) {
            $user = $beerUser->getUser();
            $beer = $beerUser->getBeer();
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
        return array(
            'beersDrunk'    => $this->getBeersDrunk($user),
            'stackedLiters' => $this->getStackedLiters($user),
            'totalLiters'   => $this->getTotalLiters($user),
            'totalBeers'    => $this->getTotalBeers($user)
        );
    }

    /**
     * Retourne une compilation des types de bières bus (camembert)
     * @param  User $user
     * @return array($position => array(Beer $beer, integer $consos),...)
     */
    private function getBeersDrunk(User $user)
    {
        $return = $counts = array();

        $beerUsers = $this->beerUserRepository->findByUser($user);
        foreach ($beerUsers as $beerUser) {
            $beer = $beerUser->getBeer();

            // Compte crédité, pas une conso
            if ($beer === null) {
                continue;
            }

            if (!isset($return[$beer->getSlug()])) {
                $return[$beer->getSlug()] = array('beer' => $beer, 'count' => 0);
                $counts[$beer->getSlug()] = 0;
            }
            $return[$beer->getSlug()]['count']++;
            $counts[$beer->getSlug()]++;
        }

        array_multisort($counts, SORT_DESC, $return);
        return array_slice($return, 0, 10);
    }

    /**
     * Retourne le nombre cumulé de litres bus au court du temps (courbe)
     * @param  User $user
     * @return array(integer $date => float $liters,...)
     */
    private function getStackedLiters(User $user)
    {
        $return = array();
        $total = 0;

        $beerUsers = $this->beerUserRepository->findByUser($user);
        foreach ($beerUsers as $beerUser) {
            $beer = $beerUser->getBeer();
            // Compte crédité, pas une conso
            if ($beer === null) {
                continue;
            }
            $total += $beer->getVolume();
            $return[$beerUser->getDate()] = $total;
        }

        return $return;
    }

    /**
     * Retourne le nombre cumulé de litres bus
     * @param  User $user
     * @return float
     */
    private function getTotalLiters(User $user)
    {
        $count = 0;

        $beerUsers = $this->beerUserRepository->findByUser($user);
        foreach ($beerUsers as $beerUser) {
            $beer = $beerUser->getBeer();
            // Compte crédité, pas une conso
            if ($beer === null) {
                continue;
            }
            $count += $beer->getVolume();
        }

        return $count;
    }

    /**
     * Retourne le nombre cumulé de bières bues
     * @param  User $user
     * @return integer
     */
    private function getTotalBeers(User $user)
    {
        $beerUsers = $this->beerUserRepository->findByUser($user);
        return count($beerUsers);
    }
}
