<?php
namespace KI\UserBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\UserBundle\Entity\User;
use KI\PublicationBundle\Entity\Post;

/**
 * Class UserRepository
 */
class UserRepository extends ResourceRepository
{
    /**
     * @param  int $userId
     * @param  array $findBy
     * @return string
     */
    public function getFollowedNewsitemsDql($userId, $findBy = [])
    {
        $dql = 'SELECT newsitem FROM
            KIPublicationBundle:Newsitem newsitem,
            KIUserBundle:User user
            WHERE user.id = ' . $userId . '
            AND (newsitem.publicationState != \'draft\' OR newsitem.authorClub IN (
                    SELECT cl FROM KIUserBundle:User us JOIN us.clubs cl WHERE us.id = user.id)
                )
            AND newsitem.name != \'message\'
            AND newsitem.authorClub NOT IN (SELECT cnf FROM KIUserBundle:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
        ';

        return $this->findByDql($dql, "newsitem", $findBy);
    }

    /**
     * @param  int $userId
     * @param  array $findBy
     * @return string
     */
    public function getFollowedEventsDql($userId, $findBy = [])
    {
        $dql = 'SELECT event FROM
            KIPublicationBundle:Event event,
            KIUserBundle:User user
            WHERE user.id = ' . $userId . '
            AND (user.id NOT IN (
                    SELECT lp FROM KIPublicationBundle:Event evt JOIN evt.listPookies lp WHERE evt.id = event.id)
                )
            AND (event.publicationState != \'draft\' OR event.authorClub IN (
                    SELECT cl FROM KIUserBundle:User us JOIN us.clubs cl WHERE us.id = user.id)
                )
            AND event.name != \'message\'
            AND event.authorClub NOT IN (SELECT cnf FROM KIUserBundle:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
        ';

        return $this->findByDql($dql, "event", $findBy);
    }

    /**
     * @param  int $userId
     * @return \KI\PublicationBundle\Entity\Event[]
     */
    public function findUpcomingFollowedEvents($userId)
    {
        return $this->getEntityManager()->createQuery('SELECT event FROM
            KIPublicationBundle:Event event,
            KIUserBundle:User user
            WHERE user.id = :userId AND event.endDate > :now AND
            (
                (event.authorUser = user.id AND event.authorClub IS NULL) OR
                user.id NOT IN (SELECT lp FROM KIPublicationBundle:Event evt JOIN evt.listPookies lp WHERE evt.id = event.id)
            )
            AND event.authorClub NOT IN (SELECT cnf FROM KIUserBundle:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
        ')
            ->setParameter('now', time())
            ->setParameter('userId', $userId)
            ->getResult();
    }

    public function getDebtsIterator()
    {
        return $this->getEntityManager()->createQuery('SELECT usr.username, usr.email, usr.promo, usr.firstName, usr.lastName, usr.balance
            FROM KIUserBundle:User usr
            WHERE usr.balance < 0
            ORDER BY usr.balance
        ')
            ->iterate();
    }

    public function getPromoBalance()
    {
        return $this->getEntityManager()->createQuery('SELECT usr.promo, SUM(usr.balance)
            FROM KIUserBundle:User usr
            GROUP BY usr.promo
            ORDER BY usr.promo
        ')
            ->getArrayResult();
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
