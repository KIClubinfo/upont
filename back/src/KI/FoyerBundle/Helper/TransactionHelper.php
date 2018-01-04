<?php

namespace KI\FoyerBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use KI\FoyerBundle\Entity\Beer;
use KI\FoyerBundle\Entity\Transaction;
use KI\FoyerBundle\Event\UserNegativeBalanceEvent;
use KI\FoyerBundle\Repository\BeerRepository;
use KI\FoyerBundle\Repository\TransactionRepository;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransactionHelper
{
    protected $beerRepository;
    protected $transactionRepository;
    protected $userRepository;
    protected $manager;
    protected $eventDispatcher;

    public function __construct(BeerRepository $beerRepository,
                                TransactionRepository $transactionRepository,
                                UserRepository $userRepository,
                                EntityManagerInterface $manager,
                                EventDispatcherInterface $eventDispatcher
    )
    {
        $this->beerRepository        = $beerRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository        = $userRepository;
        $this->manager               = $manager;
        $this->eventDispatcher       = $eventDispatcher;
    }

    /**
     * Ajoute une conso
     * @param  string $userSlug
     * @param  string $beerSlug
     * @return float  $newBalance
     * @throws NotFoundHttpException Si l'utilisateur n'est pas trouvé
     * @throws NotFoundHttpException Si la bière n'est pas trouvé
     */
    public function addBeerTransaction($userSlug, $beerSlug)
    {
        $user = $this->userRepository->findOneByUsername($userSlug);
        if (!$user instanceOf User) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        $beer = $this->beerRepository->findOneBySlug($beerSlug);
        if (!$beer instanceOf Beer) {
            throw new NotFoundHttpException('Bière non trouvée');
        }

        $amount = round(-1*$beer->getPrice(), 2);
        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setBeer($beer);
        $transaction->setAmount($amount);
        $this->manager->persist($transaction);
        $this->manager->flush();

        $this->updateBalance($user, $amount);
        return $transaction->getId();
    }

    /**
     * Crédite un compte
     * @param  string $userSlug
     * @param  float  $amount
     * @return float  $newBalance
     * @throws NotFoundHttpException Si l'utilisateur n'est pas trouvé
     */
    public function addCreditTransaction($userSlug, $amount)
    {
        $user = $this->userRepository->findOneByUsername($userSlug);
        if (!$user instanceOf User) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        $amount = round($amount, 2);
        $transaction = new Transaction();
        $transaction->setUser($user);
        $transaction->setAmount($amount);
        $this->manager->persist($transaction);
        $this->manager->flush();

        $this->updateBalance($user, $amount);
        return $transaction->getId();
    }

    /**
     * Met à jour le solde d'un utilisateur
     * @param  User   $user
     * @param  float  $amount
     * @return float  $newBalance
     */
    public function updateBalance(User $user, $amount)
    {
        $balance = $user->getBalance();
        $balance = $balance === null ? 0 : $balance;
        $newBalance = round($balance + $amount, 2);

        $user->setBalance($newBalance);
        $this->manager->flush();

        if($newBalance < 0){
            if($balance >= 0) {
                $negativeBalance = new UserNegativeBalanceEvent($user, true);
            }
            else {
                $negativeBalance = new UserNegativeBalanceEvent($user, false);
            }
            $this->eventDispatcher->dispatch('upont.negative_balance', $negativeBalance);
        }
    }

    /**
     * Recalcule le solde d'un utilisateur à partir de ses transactions
     * @param  User  $user
     * @return float $balance
     */
    public function rebuildBalance(User $user)
    {
        $transactions = $this->transactionRepository->findByUser($user);

        $balance = 0;
        foreach ($transactions as $transaction) {
            $balance += $transaction->getAmount();
        }
        $user->setBalance(round($balance, 2));
        $this->manager->flush();
        return $balance;
    }
}
