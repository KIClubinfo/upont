<?php
namespace KI\PublicationBundle\Repository;

use KI\PublicationBundle\Entity\Post;

class EventRepository extends ResourceRepository
{
    public function findAllowedEvents($userId, $publicationState = null, $limit = null, $page = null)
    {
        if ($publicationState == null) {
            $publicationState = array_keys(Post::STATE_ORDER);
        }

        $query = $this->getEntityManager()->createQuery('SELECT event FROM
            KIPublicationBundle:Event event
            WHERE
            (event.publicationState != \'draft\' OR event.authorClub IN (
                SELECT cl FROM KIUserBundle:User us JOIN us.clubs cl WHERE us.id = :userId)
            )
            AND event.publicationState IN (:publicationStates)
            ORDER BY event.date DESC
        ')
            ->setParameter('userId', $userId)
            ->setParameter('publicationState', $publicationState);

        if($limit !== null && $limit > 0) {
            $query->setMaxResults($limit);

            if ($page !== null && $page > 0)
                $query->setFirstResult(($page - 1) * $limit);
        }

        return $query->getResult();
    }
}
