<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PromoGameController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'Users');
    }

    /**
     * @ApiDoc(
     *  description="Retourne un tableau de données pour le jeu du trombinoscope",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getPromoGameAction()
    {
        $maxId = $this->em->createQuery('SELECT MAX(u.id) FROM KIUpontBundle:Users\User u')->getSingleScalarResult();
        $query = $this->em->createQuery('SELECT u FROM KIUpontBundle:Users\User u WHERE u.id >= :rand ORDER BY u.id ASC');
        $rand1 = rand(0, $maxId);

        do {
            $rand2 = rand(0, $maxId);
        } while ($rand1 == $rand2);

        do {
            $rand3 = rand(0, $maxId);
        } while ($rand3 == $rand2 || $rand3 == $rand1);

        $users = array(
            $query->setParameter('rand', $rand1)->setMaxResults(1)->getSingleResult(),
            $query->setParameter('rand', $rand2)->setMaxResults(1)->getSingleResult(),
            $query->setParameter('rand', $rand3)->setMaxResults(1)->getSingleResult()
        );

        return $this->restResponse($users);
    }
}
