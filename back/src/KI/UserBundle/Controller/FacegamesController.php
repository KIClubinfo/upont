<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\CoreBundle\Controller\ResourceController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FacegamesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Facegame', 'User');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les jeux",
     *  output="KI\UserBundle\Entity\Facegame",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getFacegamesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un jeu",
     *  output="KI\UserBundle\Entity\Facegame",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getFacegameAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée un jeu",
     *  input="KI\UserBundle\Form\FacegameType",
     *  output="KI\UserBundle\Entity\Facegame",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postFacegameAction() {
        $return = $this->postData();

        if ($return['code'] == 201) {
            $facegameHelper = $this->get('ki_user.helper.facegame');

            if (!$facegameHelper->fillUserList($return['item'])) {
                $this->manager->detach($return['item']);
                return $this->restResponse($return['item'], 400);
            }
        }
        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un jeu",
     *  input="KI\UserBundle\Form\FacegameType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function patchFacegameAction($slug)
    {
        $facegame = $this->findBySlug($slug);

        $request = $this->getRequest()->request;
        if (!$request->has('wrongAnswers')) {
            throw new BadRequestHttpException('Paramètre manquant');
        }

        $facegameHelper = $this->get('ki_user.helper.facegame');
        $facegameHelper->endGame($facegame, $request->get('wrongAnswers'));

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  resource=false,
     *  description="Renvoie les statistiques globales sur la Réponse D",
     *  output="KI\UserBundle\Entity\Facegame",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/statistics/facegame")
     */
    public function getGlobalStatisticsAction()
    {
        $facegameStatisticsHelper = $this->get('ki_user.helper.facegame_statistics');
        return $this->jsonResponse($facegameStatisticsHelper->globalStatistics());
    }

    /**
     * @ApiDoc(
     *  description="Renvoie les statistiques d'un utilisateur sur la Réponse D",
     *  output="KI\UserBundle\Entity\Facegame",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/statistics/facegame/{slug}")
     */
    public function getUserStatisticsAction($slug)
    {
        $repository = $this->get('ki_user.repository.user');
        $user = $repository->findOneByUsername($slug);

        if ($user === null) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        $facegameStatisticsHelper = $this->get('ki_user.helper.facegame_statistics');
        return $this->jsonResponse($facegameStatisticsHelper->userStatistics($user));
    }
}
