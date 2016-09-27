<?php
namespace KI\UserBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\UserBundle\Entity\User;

class AdmissibleRepository extends ResourceRepository
{
    public function getCurrentYearAdmissibles()
    {
        return $this->createQueryBuilder('admissible')
            ->where('admissible.year = :year')
            ->setParameter('year', strftime('%Y'))
            ->getQuery()
            ->getResult();
    }
}
