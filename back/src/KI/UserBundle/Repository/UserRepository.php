<?php
namespace KI\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use DateTime;

/**
 * Class UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * @param  int $userId
     * @param  int $limit
     * @param  int $page
     * @return \KI\PublicationBundle\Entity\Event[]
     */
    public function findFollowedEvents($userId, $limit = null, $page = null)
    {
         $query = $this->getEntityManager()->createQuery('SELECT event FROM
            KIPublicationBundle:Event event,
            KIUserBundle:Club club,
            KIUserBundle:User user
            WHERE user.id = :userId AND
            (
                (event.authorUser = user.id AND event.authorClub IS NULL) OR
                (event.authorClub = club.id AND user.id NOT IN (
                    SELECT lp FROM KIPublicationBundle:Event evt JOIN evt.listPookies lp WHERE evt.id = event.id)
                )
            )
            AND event.authorClub NOT IN (SELECT cnf FROM KIUserBundle:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
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

    /**
     * @param  int $userId
     * @return \KI\PublicationBundle\Entity\Event[]
     */
    public function findUpcomingFollowedEvents($userId)
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
            AND event.authorClub NOT IN (SELECT cnf FROM KIUserBundle:User usr JOIN usr.clubsNotFollowed cnf WHERE usr.id = user.id)
        ')
            ->setParameter('now', time())
            ->setParameter('userId', $userId)
            ->getResult();
    }
}
