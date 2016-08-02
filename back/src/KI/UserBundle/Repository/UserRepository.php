<?php
namespace KI\UserBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\UserBundle\Entity\User;

/**
 * Class UserRepository
 */
class UserRepository extends ResourceRepository
{
    /**
     * @param  int $userId
     * @return \KI\PublicationBundle\Entity\Event[]
     */
    public function findAllFollowedEvents($userId)
    {
        return $this->getEntityManager()->createQuery('SELECT event FROM
            KIPublicationBundle:Event event,
            KIUserBundle:Club club,
            KIUserBundle:User user
            WHERE user.id = :userId AND event.endDate > :now AND
            (
                (event.authorUser = user.id AND event.authorClub IS NULL) OR
                (event.authorClub = club.id AND user.id NOT IN (
                    SELECT lp FROM KIPublicationBundle:Event evt JOIN evt.listPookies lp WHERE evt.id = event.id)
                )
            )
            AND event.id NOT IN (SELECT cnf FROM KIUserBundle:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
        ')
            ->setParameter('now', time())
            ->setParameter('userId', $userId)
            ->getResult();
    }

    public function getDebtsIterator()
    {
        $this->createQueryBuilder('u')
            ->select('u.username, u.email, u.promo, u.firstName, u.lastName, u.balance')
            ->where('u.balance < 0')
            ->orderBy('u.balance')
            ->getQuery()
            ->iterate();
    }

    public function getUserClubs(User $user)
    {
        return $this->getEntityManager()->createQuery('SELECT cu, club
            FROM KIUserBundle:ClubUser cu
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
