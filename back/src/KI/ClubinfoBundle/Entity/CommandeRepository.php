<?php

namespace KI\ClubinfoBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CentraleRepository extends EntityRepository
{
    public function findOneBySlugAndUsername($slug, $username)
    {

        $em = $this
          ->getDoctrine()
          ->getManager();
        $centrale = $em->getRepository('KIClubinfoBundle:Centrale')
          ->findOneBySlug($slug);
        $user = $em->getRepository('KIUserBundle:User')
          ->findOneBySlug($username);

        $qb = $this->createQueryBuilder('a');

        $qb->where('a.centrale = :centrale')
            ->setParameter('centrale', $centrale)
            ->andWhere('a.user = :user')
            ->setParameter('user', $user)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySlug($slug)
    {

        $em = $this
          ->getDoctrine()
          ->getManager();
        $centrale = $em->getRepository('KIClubinfoBundle:Centrale')
          ->findOneBySlug($slug);

        $qb = $this->createQueryBuilder('a');

        $qb->where('a.centrale = :centrale')
            ->setParameter('centrale', $centrale)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUsername($username)
    {

        $em = $this
          ->getDoctrine()
          ->getManager();
        $user = $em->getRepository('KIUserBundle:User')
          ->findOneBySlug($username);

        $qb = $this->createQueryBuilder('a');

        $qb->where('a.user = :user')
            ->setParameter('user', $user)
        ;

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }
}
