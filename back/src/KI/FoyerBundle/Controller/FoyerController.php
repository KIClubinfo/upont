<?php

namespace KI\FoyerBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FoyerController extends \KI\CoreBundle\Controller\BaseController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'User');
    }

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
        if ($this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            throw new AccessDeniedException();

        $service = $this->get('ki_foyer.service.foyer');
        $service->initialize();

        if ($service->hasFailed())
            return $this->jsonResponse('Erreur - impossible de déterminer le solde');

        return $this->jsonResponse(array('balance' => $service->balance()));
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
    public function statisticsAction($slug)
    {
        if ($this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            throw new AccessDeniedException();

        $repo = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($slug);
        $service = $this->get('ki_foyer.service.foyer');
        $service->initialize($user);

        if ($service->hasFailed())
            return $this->jsonResponse(array('error' => 'Impossible d\'afficher les statistiques Foyer'));

        return $this->jsonResponse($service->statistics());
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
     */
    public function statisticsMainAction()
    {
        if ($this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            throw new AccessDeniedException();

        $service = $this->get('ki_foyer.service.foyer');
        $service->initialize();

        return $this->jsonResponse($service->rankings());
    }
}
