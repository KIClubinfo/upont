<?php

namespace App\Controller\Ponthub;

use App\Controller\ResourceController;
use App\Entity\Request;
use App\Form\RequestType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class RequestsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Request::class, RequestType::class);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Liste les demandes d'ajout de fichier",
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
     * @Route("/requests", methods={"GET"})
     */
    public function getRequestsAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne une demande d'ajout de fichier",
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
     * @Route("/requests/{slug}", methods={"GET"})
     */
    public function getRequestAction($slug)
    {
        $request = $this->getOne($slug);

        return $this->json($request);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Crée une demande d'ajout de fichier",
     *     @SWG\Parameter(
     *         name="name",
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
     * @Route("/requests", methods={"POST"})
     */
    public function postRequestAction()
    {
        $data = $this->post($this->is('USER'));

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Supprime une demande d'ajout de fichier",
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
     * @Route("/requests/{slug}", methods={"DELETE"})
     */
    public function deleteRequestAction($slug)
    {
        $this->delete($slug, $this->is('USER'));

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Approuve une demande d'ajout de fichier",
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
     * @Route("/requests/{slug}/upvote", methods={"PATCH"})
     */
    public function upvoteRequestAction($slug)
    {
        $item = $this->getOne($slug);
        $item->setVotes($item->getVotes() + 1);
        $this->manager->flush();

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Désapprouve une demande d'ajout de fichier",
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
     * @Route("/requests/{slug}/downvote", methods={"PATCH"})
     */
    public function downvoteRequestAction($slug)
    {
        $item = $this->getOne($slug);
        $item->setVotes($item->getVotes() - 1);
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
