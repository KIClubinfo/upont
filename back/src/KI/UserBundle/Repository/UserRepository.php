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
     * @return \KI\PublicationBundle\Entity\Event[]
     */
    public function findAllFollowedEvents($userId)
    {
        return $this->getEntityManager()->createQuery("SELECT event FROM
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
        ")
            ->setParameter('now', new DateTime())
            ->setParameter('userId', $userId)
            ->getResult();
    }
}
