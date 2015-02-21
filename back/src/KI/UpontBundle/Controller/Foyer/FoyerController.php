<?php

namespace KI\UpontBundle\Controller\Foyer;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class FoyerController extends \KI\UpontBundle\Controller\Core\BaseController
{
    /**
     * @ApiDoc(
     *  description="Retourne le solde du Foyer de l'utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   409="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function balanceAction()
    {
        $service = $this->get('ki_upont.foyer');
        $service->initialize();

        if ($service->hasFailed())
            return $this->jsonResponse(null, 409);

        return $this->jsonResponse($service->balance());
    }

    /**
     * @ApiDoc(
     *  description="Retourne un classement des plus gros buveurs du Foyer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   409="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function rankingsAction()
    {
        $service = $this->get('ki_upont.foyer');
        $service->initialize();

        if ($service->hasFailed())
            return $this->jsonResponse(null, 409);

        return $this->jsonResponse($service->rankings());
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
     */
    public function statisticsAction()
    {
        $service = $this->get('ki_upont.foyer');
        $service->initialize();

        if ($service->hasFailed())
            return $this->jsonResponse(null, 409);

        return $this->jsonResponse($service->statistics());
    }
}
