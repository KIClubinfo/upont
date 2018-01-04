<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Helper\FacegameHelper;
use KI\UserBundle\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @Route("/facegames")
     * @Method("GET")
     */
    public function getFacegamesAction()
    {
        return $this->getAll();
    }

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
     * @Route("/facegames/{slug}")
     * @Method("GET")
     */
    public function getFacegameAction($slug)
    {
        $facegame = $this->getOne($slug);

        return $this->json($facegame);
    }

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
     * @Route("/facegames")
     * @Method("POST")
     */
    public function postFacegameAction(FacegameHelper $facegameHelper)
    {
        $data = $this->post($this->is('USER'));

        if ($data['code'] == 201) {
            if (!$facegameHelper->fillUserList($data['item'])) {
                $this->manager->detach($data['item']);
                return $this->json($data['item'], 400);
            }
        }
        return $this->formJson($data);
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
     * @Route("/facegames/{slug}")
     * @Method("PATCH")
     */
    public function patchFacegameAction(FacegameHelper $facegameHelper, Request $request, $slug)
    {
        $facegame = $this->findBySlug($slug);

        if (!$request->request->has('wrongAnswers') || !$request->request->has('duration')) {
            throw new BadRequestHttpException('Paramètre manquant');
        }

        $facegameHelper->endGame($facegame, $request->request->get('wrongAnswers'), $request->request->get('duration'));

        return $this->json(null, 204);
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
     * @Route("/statistics/facegame")
     * @Method("GET")
     */
    public function getGlobalStatisticsAction()
    {
        return $this->json([
            'totalNormal'        => $this->repository->getNormalGamesCount(),
            'totalHardcore'      => $this->repository->getHardcoreGamesCount(),
            'normalHighscores'   => $this->repository->getNormalHighscores(),
            'hardcoreHighscores' => $this->repository->getHardcoreHighscores(),
        ]);
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
     * @Route("/statistics/facegame/{slug}")
     * @Method("GET")
     */
    public function getUserStatisticsAction(UserRepository $userRepository, $slug)
    {
        $user = $userRepository->findOneByUsername($slug);

        if ($user === null) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        return $this->json([
            'totalNormal'        => $this->repository->getUserGamesCount($user, 0),
            'totalHardcore'      => $this->repository->getUserGamesCount($user, 1),
            'normalHighscores'   => $this->repository->getUserHighscores($user, 0),
            'hardcoreHighscores' => $this->repository->getUserHighscores($user, 1),
        ]);
    }
}
