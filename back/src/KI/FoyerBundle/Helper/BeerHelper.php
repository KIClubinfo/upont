<?php

namespace KI\FoyerBundle\Helper;

use Doctrine\ORM\EntityRepository;
use KI\FoyerBundle\Entity\Beer;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\AchievementCheckEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BeerHelper
{
    protected $beerRepository;
    protected $beerUserRepository;
    protected $userRepository;
    protected $dispatcher;

    public function __construct(EntityRepository $beerRepository,
                                EntityRepository $beerUserRepository,
                                EntityRepository $userRepository,
                                EventDispatcherInterface $dispatcher)
    {
        $this->beerRepository     = $beerRepository;
        $this->beerUserRepository = $beerUserRepository;
        $this->userRepository     = $userRepository;
        $this->dispatcher         = $dispatcher;
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
        $beerUsers = $this->beerUserRepository->findBy(array(), array('date' => 'DESC'), 500);

        $counts = array();
        foreach ($beerUsers as $beerUser) {
            // On peut tomber sur une entrée "compte crédité"
            if ($beerUser->getBeer() === null) {
                continue;
            }
            $beerId = $beerUser->getBeer()->getId();

            if (!isset($counts[$beerId])) {
                $counts[$beerId] = 0;
            }

            $counts[$beerId] = $counts[$beerId] + 1;
        }

        // On trie
        $return = $beerCounts = array();
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
        $beerUsers = $this->beerUserRepository->findBy(array(), array('date' => 'DESC'), 500);

        // On veut positionner le compte Externe Foyer en première positionn
        $users = array();
        $users[] = $this->userRepository->findOneByUsername('externe-foyer');

        foreach ($beerUsers as $beerUser) {
            $user = $beerUser->getUser();

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
            $listUsers = $this->userRepository->findBy(array(), array('id' => 'DESC'), 100);

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

    /**
     * Met à jour le compte d'un élève
     * @param  string $userSlug [description]
     * @param  string $beerSlug
     * @param  boolean $add  Si la conso est comptée positivement ou négativement
     * @return array(User, Beer)
     * @throws NotFoundHttpException Si l'utilisateur n'est pas trouvé
     * @throws NotFoundHttpException Si la bière n'est pas trouvé
     */
    public function updateBalance($userSlug, $beerSlug, $add = false)
    {
        $user = $this->userRepository->findOneByUsername($userSlug);
        if (!$user instanceOf User) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        $beer = $this->beerRepository->findOneBySlug($beerSlug);
        if (!$beer instanceOf Beer) {
            throw new NotFoundHttpException('Bière non trouvée');
        }

        $balance = $user->getBalance();
        $balance = $balance === null ? 0 : $balance;
        $price   = $beer->getPrice();
        $balance = $add ? $balance + $price : $balance - $price;
        $user->setBalance($balance);

        return array($user, $beer);
    }
}
