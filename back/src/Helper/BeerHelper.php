<?php

namespace App\Helper;

use App\Entity\Beer;
use App\Repository\BeerRepository;
use App\Repository\TransactionRepository;
use App\Entity\User;
use App\Repository\UserRepository;

class BeerHelper
{
    protected $beerRepository;
    protected $transactionRepository;
    protected $userRepository;

    public function __construct(BeerRepository $beerRepository,
                                TransactionRepository $transactionRepository,
                                UserRepository $userRepository)
    {
        $this->beerRepository        = $beerRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository        = $userRepository;
    }

    /**
     * Trie les bières par ordre décroissant de consommation
     * @return Beer[]
     */
    public function getBeerOrderedList()
    {
        // On commence par récupérer toutes les bières
        $beers = $this->beerRepository->findAll();

        // On va établir les comptes sur les 500 dernières consos
        $transactions = $this->transactionRepository->findBy([], ['date' => 'DESC'], 500);

        $counts = [];
        foreach ($transactions as $transaction) {
            // On peut tomber sur une entrée "compte crédité"
            if ($transaction->getBeer() === null) {
                continue;
            }
            $beerId = $transaction->getBeer()->getId();

            if (!isset($counts[$beerId])) {
                $counts[$beerId] = 0;
            }

            $counts[$beerId] = $counts[$beerId] + 1;
        }

        // On trie
        $return = $beerCounts = [];
        foreach ($beers as $beer) {
            $beerId = $beer->getId();

            $beerCounts[] = isset($counts[$beerId]) ? $counts[$beerId] : 0;
            $return[]     = $beer;
        }
        array_multisort($beerCounts, SORT_DESC, $return);

        return $return;
    }

    /**
     * Trie les utilisateurs par ordre décroissant de date de consommation
     * @return User[]
     */
    public function getUserOrderedList()
    {
        // On commence par récupérer 500 dernières consos
        $transactions = $this->transactionRepository->findBy([], ['date' => 'DESC'], 500);

        // On veut positionner le compte Externe Foyer en première positionn
        $users = [];
        $users[] = $this->userRepository->findOneByUsername('externe-foyer');

        foreach ($transactions as $transaction) {
            $user = $transaction->getUser();

            if (!in_array($user, $users)) {
                $users[] = $user;
            }
            // On ne veut que 48 résultats
            if (count($users) >= 48) {
                break;
            }
        }

        // On complète avec d'autres utilisateurs au besoin
        if (count($users) < 48) {
            $listUsers = $this->userRepository->findBy([], ['id' => 'DESC'], 100);

            foreach ($listUsers as $user) {
                if (!in_array($user, $users)) {
                    $users[] = $user;
                }
                // On ne veut que 48 résultats
                if (count($users) >= 48) {
                    break;
                }
            }
        }
        return $users;
    }
}
