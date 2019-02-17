<?php

namespace App\Controller\Publications;

use App\Controller\ResourceController;
use App\Entity\Newsitem;
use App\Form\NewsitemType;
use App\Listener\NewsitemListener;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class NewsitemsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Newsitem::class, NewsitemType::class);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Liste les news",
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
     * @Route("/newsitems", methods={"GET"})
     */
    public function getNewsitemsAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Retourne une news",
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
     * @Route("/newsitems/{slug}", methods={"GET"})
     */
    public function getNewsitemAction($slug)
    {
        $newsitem = $this->getOne($slug);

        return $this->json($newsitem);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Crée une news",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="text",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="sendMail",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean"
     *     ),
     *     @SWG\Parameter(
     *         name="authorClub",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="uploadedFiles",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="file"
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
     * @Route("/newsitems", methods={"POST"})
     */
    public function postNewsitemAction(NewsitemListener $newsitemListener)
    {
        $data = $this->post($this->isClubMember());

        if ($data['code'] == 201) {
            $newsitemListener->postPersist($data['item']);
        }

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Modifie une news",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="text",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="sendMail",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="boolean",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="authorClub",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="uploadedFiles",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="file",
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
     * @Route("/newsitems/{slug}", methods={"PATCH"})
     */
    public function patchNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        $data = $this->patch($slug, $this->isClubMember($club));

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime une news",
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
     * @Route("/newsitems/{slug}", methods={"DELETE"})
     */
    public function deleteNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;

        $this->delete($slug, $this->isClubMember($club));

        return $this->json(null, 204);
    }
}
