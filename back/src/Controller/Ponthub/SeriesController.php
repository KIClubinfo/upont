<?php

namespace App\Controller\Ponthub;

use App\Entity\Episode;
use App\Entity\Serie;
use App\Form\SerieType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class SeriesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Serie::class, SerieType::class);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Liste les séries",
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
     * @Route("/series", methods={"GET"})
     */
    public function getSeriesAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne une série",
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
     * @Route("/series/{slug}", methods={"GET"})
     */
    public function getSerieAction(Serie $serie)
    {
        return $this->json($serie);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Modifie une série",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="description",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="actors",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="genres",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="tags",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="duration",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="director",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="rating",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="year",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
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
     * @Route("/series/{slug}", methods={"PATCH"})
     */
    public function patchSerieAction(Serie $serie)
    {
        $data = $this->patchItem($serie, $this->is('JARDINIER'));

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime une série",
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
     * @Route("/series/{slug}", methods={"DELETE"})
     */
    public function deleteSerieAction(Serie $serie)
    {
        $this->deleteItem($serie, $this->is('JARDINIER'));

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Liste les épisodes d'une série",
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
     * @Route("/series/{slug}/episodes", methods={"GET"})
     */
    public function getSerieEpisodesAction(Serie $serie)
    {
        $episodes = $serie->getEpisodes();

        return $this->json($episodes);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne un épisode d'une série",
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
     * @Route("/series/{slug}/episodes/{episode_slug}", methods={"GET"})
     * @ParamConverter("episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function getSerieEpisodeAction(Serie $serie, Episode $episode)
    {
        return $this->json($episode);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Modifie un épisode d'une série",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
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
     * @Route("/series/{slug}/episodes/{episode_slug}", methods={"PATCH"})
     * @ParamConverter("episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function patchSerieEpisodeAction(Serie $serie, Episode $episode)
    {
        $data = $this->patchItem($episode, $this->is('JARDINIER'));

        return $this->formJson($data);
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
     * @Route("/series/{slug}/episodes/{episode_slug}/download", methods={"GET"})
     * @ParamConverter("episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function downloadEpisodeAction(Serie $serie, Episode $episode)
    {
        return $this->download($episode);
    }
}
