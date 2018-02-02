<?php
namespace KI\PublicationBundle\Repository;

use KI\CoreBundle\Repository\ResourceRepository;
use KI\PublicationBundle\Entity\Post;

class NewsitemRepository extends ResourceRepository
{
    /**
     * @param  int $userId
     * @param  array $findBy
     * @return string
     */
    public function getAllowedNewsitemsDql($userId, $findBy = [])
    {
        $dql = 'SELECT newsitem FROM
            KIPublicationBundle:Newsitem newsitem
            WHERE
            (newsitem.publicationState != \'draft\' OR newsitem.authorClub IN (
                SELECT cl FROM KIUserBundle:User us JOIN us.clubs cl WHERE us.id = ' . $userId . ')
            )
            AND newsitem.name != \'message\'
        ';

        return $this->findByDql($dql, "newsitem", $findBy);
    }
}
