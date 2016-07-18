<?php
namespace KI\FoyerBundle\Repository;

use Doctrine\ORM\EntityRepository;
use DateTime;
use KI\FoyerBundle\Entity\Transaction;

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
}
