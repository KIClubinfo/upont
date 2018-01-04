<?php
namespace KI\FoyerBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use KI\FoyerBundle\Entity\Transaction;
use KI\UserBundle\Entity\User;

class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @return object[]
     */
    public function getHallOfFame()
    {
        $sept1 = strtotime("September 1st");
        if ($sept1 > time()) {
            $sept1 = strtotime("September 1st -1Year");
        }

        $hallOfFame = $this->getEntityManager()->createQuery('SELECT usr AS user, SUM(beer.volume) AS liters FROM
            KIUserBundle:User usr,
            KIFoyerBundle:Transaction transac,
            KIFoyerBundle:Beer beer
            WHERE transac.user = usr
            AND transac.beer = beer
            AND transac.beer IS NOT NULL
            AND usr.balance > 0
            AND transac.date > :schoolYear
            GROUP BY usr.id
            ORDER BY liters DESC
            ')
            ->setParameter('schoolYear', $sept1)
            ->setMaxResults(10)
            ->getResult();

        foreach ($hallOfFame as &$data){
            $data['liters'] = round($data['liters'], 2);
        }

        return $hallOfFame;
    }

    /**
     * @return object[]
     */
    public function getPromoBalances()
    {
        $promoBalances = $this->getEntityManager()->createQuery('SELECT
            SUM(usr.balance) AS promoBalance, usr.promo as promo FROM
            KIUserBundle:User usr
            GROUP BY promo
            ORDER BY promo ASC
            ')
            ->getArrayResult();

        foreach ($promoBalances as &$promoBalance){
            $promoBalance['promoBalance'] = round($promoBalance['promoBalance'], 2);
        }

        return $promoBalances;
    }

    /**
     * @return object[]
     */
    public function getSoldBeers()
    {
        $sept1 = strtotime("September 1st");
        if ($sept1 > time()) {
            $sept1 = strtotime("September 1st -1Year");
        }

        $soldBeers = $this->getEntityManager()->createQuery('SELECT
            COUNT(beer.id) AS soldBeer, beer.name as name FROM
            KIFoyerBundle:Transaction transac,
            KIFoyerBundle:Beer beer
            WHERE transac.beer = beer
            AND transac.beer IS NOT NULL
            AND transac.date > :schoolYear
            GROUP BY beer.id
            ORDER BY soldBeer DESC
            ')
            ->setParameter('schoolYear', $sept1)
            ->getArrayResult();

        return $soldBeers;
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
