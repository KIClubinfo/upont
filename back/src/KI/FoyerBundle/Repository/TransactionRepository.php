<?php
namespace KI\FoyerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use KI\FoyerBundle\Entity\Transaction;
use KI\UserBundle\Entity\User;

class TransactionRepository extends EntityRepository
{
    /**
     * @return object[]
     */
    public function getHallOfFame()
    {
        $hallOfFame = $this->getEntityManager()->createQuery('SELECT usr AS user, SUM(beer.volume) AS liters FROM
            KIUserBundle:User usr,
            KIFoyerBundle:Transaction transac, 
            KIFoyerBundle:Beer beer
            WHERE transac.user = usr
            AND transac.beer = beer            
            AND transac.beer IS NOT NULL
            AND usr.balance > 0
            GROUP BY usr.id 
            ORDER BY liters DESC
            ')
            ->setMaxResults(10)
            ->getResult();

        foreach ($hallOfFame as &$data){
            $data['liters'] = round($data['liters'], 2);
        }

        return $hallOfFame;
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

        $transactions = $this->findBy(['user' => $user], ['date' => 'ASC']);

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
