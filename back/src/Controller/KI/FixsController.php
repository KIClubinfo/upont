<?php

namespace App\Controller\KI;

use App\Controller\ResourceController;
use App\Entity\Fix;
use App\Form\FixType;
use App\Service\NotifyService;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class FixsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Fix::class, FixType::class);
    }

    /**
     * @Operation(
     *     tags={"Clubinfo"},
     *     summary="Liste les tâches de dépannage",
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
     * @Route("/fixs", methods={"GET"})
     */
    public function getFixsAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Clubinfo"},
     *     summary="Retourne une tâche de dépannage",
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
     * @Route("/fixs/{slug}", methods={"GET"})
     */
    public function getFixAction($slug)
    {
        $fix = $this->getOne($slug);

        return $this->json($fix);
    }

    /**
     * @Operation(
     *     tags={"Clubinfo"},
     *     summary="Crée une tâche de dépannage",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="problem",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="fix",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
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
     * @Route("/fixs", methods={"POST"})
     */
    public function postFixAction()
    {
        $data = $this->post($this->is('USER'));

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Clubinfo"},
     *     summary="Modifie une tâche de dépannage",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="problem",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="fix",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="boolean",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="status",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
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
     * @Route("/fixs/{slug}", methods={"PATCH"})
     */
    public function patchFixAction(NotifyService $notifyService, $slug)
    {
        $data = $this->patch($slug, $this->isClubMember('ki'));

        $fix = $data['item'];

        if ($fix->getFix()) {
            $notifyService->notify(
                'notif_fixs',
                'Demande de dépannage',
                'Ta demande de dépannage a été actualisée par le KI !',
                'to',
                [$fix->getUser()]
            );
        }

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Clubinfo"},
     *     summary="Supprime une tâche de dépannage",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
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
     * @Route("/fixs/{slug}", methods={"DELETE"})
     */
    public function deleteFixAction($slug)
    {
        $fix = $this->getOne($slug);
        $this->delete($slug,
            $this->user->getUsername() == $fix->getUser()->getUsername() || $this->isClubMember('ki')
        );

        return $this->json(null, 204);
    }
}
