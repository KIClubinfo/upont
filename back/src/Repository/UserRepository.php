<?php
namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

use App\Entity\User;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param  int $userId
     * @param  int $limit
     * @param  int $page
     * @return \App\Entity\Event[]
     */
    public function findFollowedEvents($userId, $limit = null, $page = null)
    {
        $query = $this->getEntityManager()->createQuery('SELECT event FROM
            App:Event event,
            App:Club club,
            App:User user
            WHERE user.id = :userId AND
            (
                (event.authorUser = user.id AND event.authorClub IS NULL) OR
                (event.authorClub = club.id AND user.id NOT IN (
                    SELECT lp FROM App:Event evt JOIN evt.listPookies lp WHERE evt.id = event.id)
                )
            )
            AND event.authorClub NOT IN (SELECT cnf FROM App:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
            ORDER BY event.date DESC
        ')
            ->setParameter('userId', $userId);

        if($limit !== null && $limit > 0) {
            $query->setMaxResults($limit);

            if ($page !== null && $page > 0)
                $query->setFirstResult(($page - 1) * $limit);
        }

        return $query->getResult();
    }

    public function countFollowedEvents($userId)
    {
        $query = $this->getEntityManager()->createQuery('SELECT COUNT(event) FROM
            App:Event event,
            App:Club club,
            App:User user
            WHERE user.id = :userId AND
            (
                (event.authorUser = user.id AND event.authorClub IS NULL) OR
                (event.authorClub = club.id AND user.id NOT IN (
                    SELECT lp FROM App:Event evt JOIN evt.listPookies lp WHERE evt.id = event.id)
                )
            )
            AND event.authorClub NOT IN (SELECT cnf FROM App:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
            ORDER BY event.date DESC
        ')
            ->setParameter('userId', $userId);

        return $query->getSingleScalarResult();
    }

    public function getDebtsIterator()
    {
        return $this->getEntityManager()->createQuery('SELECT usr.username, usr.email, usr.promo, usr.firstName, usr.lastName, usr.balance
            FROM App:User usr
            WHERE usr.balance < 0
            ORDER BY usr.balance
        ')
            ->iterate();
    }

    public function getPromoBalance()
    {
        return $this->getEntityManager()->createQuery('SELECT usr.promo, SUM(usr.balance)
            FROM App:User usr
            GROUP BY usr.promo
            ORDER BY usr.promo
        ')
            ->getArrayResult();
    }

    public function getUserClubs(User $user)
    {
        return $this->getEntityManager()->createQuery('SELECT cu, club
            FROM App:ClubUser cu
            JOIN cu.club club
            WHERE cu.user = :user
        ')
            ->setParameter('user', $user)
            ->getResult();
    }

    public function getOnlineUsers($delay = 30) {
        return $this->createQueryBuilder('u')
            ->where('u.lastConnect > :date')
            ->setParameter('date', time() - $delay * 60)
            ->getQuery()
            ->getResult();
    }
}
