<?php

namespace App\Controller\Ponthub;

use App\Entity\Other;
use App\Form\OtherType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class OthersController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Other::class, OtherType::class);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Liste les fichiers autres",
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
     * @Route("/others", methods={"GET"})
     */
    public function getOthersAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne un fichier autre",
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
     * @Route("/others/{slug}", methods={"GET"})
     */
    public function getOtherAction($slug)
    {
        $other = $this->getOne($slug);

        return $this->json($other);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Modifie un fichier autre",
     *     @SWG\Parameter(
     *         name="name",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="description",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="genres",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="tags",
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
     * @Route("/others/{slug}", methods={"PATCH"})
     */
    public function patchOtherAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

        return $this->formJson($data);
    }


    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime un autre",
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
     * @Route("/others/{slug}", methods={"DELETE"})
     */
    public function deleteOtherAction($slug)
    {
        $this->delete($slug, $this->is('JARDINIER'));

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Télécharge un fichier sur Ponthub, et log le téléchargement",
     *     @SWG\Response(
     *         response="200",
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
     * @Route("/others/{slug}/download", methods={"GET"})
     */
    public function downloadOtherAction($slug)
    {
        $item = $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
