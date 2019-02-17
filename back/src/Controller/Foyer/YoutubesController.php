<?php

namespace App\Controller\Foyer;

use App\Controller\ResourceController;
use App\Entity\Youtube;
use App\Form\YoutubeType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class YoutubesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Youtube::class, YoutubeType::class);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Liste les liens Youtube",
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
     * @Route("/youtubes", methods={"GET"})
     */
    public function getYoutubesAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne un lien Youtube",
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
     * @Route("/youtubes/{slug}", methods={"GET"})
     */
    public function getYoutubeAction($slug)
    {
        $youtube = $this->getOne($slug);

        return $this->json($youtube);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Crée un lien Youtube",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="link",
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
     * @Route("/youtubes", methods={"POST"})
     */
    public function postYoutubeAction()
    {
        $data = $this->post($this->is('USER'));

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Supprime un lien Youtube",
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
     * @Route("/youtubes/{slug}", methods={"DELETE"})
     */
    public function deleteYoutubeAction($slug)
    {
        $author = $this->findBySlug($slug)->getUser();
        $this->delete($slug, $this->user == $author);

        return $this->json(null, 204);
    }
}
