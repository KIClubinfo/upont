<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Beer;
use App\Repository\BeerRepository;
use App\Repository\TransactionRepository;
use App\Entity\User;
use App\Repository\UserRepository;

class BeerHelper
{
    protected $transactionRepository;
    protected $userRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        UserRepository $userRepository
    )
    {
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Trie les utilisateurs par ordre décroissant de date de consommation
     * @return User[]
     */
    public function getUserOrderedList()
    {
        // TODO SQL, plus délicat

        // On commence par récupérer 500 dernières consos
        $transactions = $this->transactionRepository->findBy([], ['date' => 'DESC'], 500);

        // On veut positionner le compte Externe Foyer en première positionn
        $users = [];
        $users[] = $this->userRepository->findOneByUsername('externe-foyer');

        foreach ($transactions as $transaction) {
            $user = $transaction->getUser();
            if ($user === null) {
                continue;
            }

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
