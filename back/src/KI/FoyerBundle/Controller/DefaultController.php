<?php

namespace KI\FoyerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\CoreBundle\Controller\BaseController;

class DefaultController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'User');
    }

    /**
     * @ApiDoc(
     *  description="Retourne des statistiques Foyer de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   409="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/statistics/foyer/{slug}")
     */
    public function getStatisticsAction($slug)
    {
        $this->trust(!$this->is('EXTERIEUR'));

        $user = $this->findBySlug($slug);

        if (!$user->getStatsFoyer()) {
            return $this->jsonResponse(array(), 200);
        }
        $statisticsHelper = $this->get('ki_foyer.helper.statistics');
        $statistics = $statisticsHelper->getUserStatistics($user);

        return $this->restResponse($statistics);
    }

    /**
     * @ApiDoc(
     *  description="Retourne des statistiques générales sur le Foyer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/statistics/foyer")
     */
    public function getStatisticsMainAction()
    {
        $this->trust(!$this->is('EXTERIEUR'));

        $statisticsHelper = $this->get('ki_foyer.helper.statistics');
        $statistics = $statisticsHelper->getMainStatistics();

        return $this->restResponse($statistics);
    }
}
