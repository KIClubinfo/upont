<?php

namespace App\Controller;

use App\Entity\Facegame;
use App\Form\FacegameType;
use App\Helper\FacegameHelper;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class FacegamesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Facegame::class, FacegameType::class);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne un jeu",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/facegames/{slug}", methods={"GET"})
     */
    public function getFacegameAction($slug)
    {
        $facegame = $this->getOne($slug);

        return $this->json($facegame);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Crée un jeu",
     *     @SWG\Parameter(
     *         name="promo",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="duration",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="hardcore",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/facegames", methods={"POST"})
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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Modifie un jeu",
     *     @SWG\Parameter(
     *         name="promo",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="duration",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="hardcore",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="boolean",
     *         schema=""
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/facegames/{slug}", methods={"PATCH"})
     */
    public function patchFacegameAction(FacegameHelper $facegameHelper, Request $request, $slug)
    {
        $facegame = $this->findBySlug($slug);

        if (!$request->request->has('wrongAnswers') || !$request->request->has('duration')) {
            throw new BadRequestHttpException('Paramètre manquant');
        }

        // FIXME TRICHE POSSIBLE
        $facegameHelper->endGame($facegame, $request->request->get('wrongAnswers'), $request->request->get('duration'));

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie les statistiques globales sur la Réponse D",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/statistics/facegame", methods={"GET"})
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
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Renvoie les statistiques d'un utilisateur sur la Réponse D",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/statistics/facegame/{slug}", methods={"GET"})
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
