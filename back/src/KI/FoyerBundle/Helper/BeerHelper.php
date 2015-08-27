<?php

namespace KI\FoyerBundle\Helper;

use Doctrine\ORM\EntityRepository;

class BeerHelper
{
    protected $beerRepository;
    protected $beerUserRepository;

    public function __construct(EntityRepository $beerRepository, EntityRepository $beerUserRepository)
    {
        $this->beerRepository     = $beerRepository;
        $this->beerUserRepository = $beerUserRepository;
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
}
