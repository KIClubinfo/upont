<?php

namespace App\Controller\Ponthub;

use App\Entity\Movie;
use App\Form\MovieType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Movie::class, MovieType::class);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Liste les films",
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
     * @Route("/movies", methods={"GET"})
     */
    public function getMoviesAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne un film",
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
     * @Route("/movies/{slug}", methods={"GET"})
     */
    public function getMovieAction($slug)
    {
        $movie = $this->getOne($slug);

        return $this->json($movie);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Modifie un film",
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
     *         name="actors",
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
     *         name="duration",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="director",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="rating",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="year",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="integer",
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
     * @Route("/movies/{slug}", methods={"PATCH"})
     */
    public function patchMovieAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime un film",
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
     * @Route("/movies/{slug}", methods={"DELETE"})
     */
    public function deleteMovieAction($slug)
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
     * @Route("/movies/{slug}/download", methods={"GET"})
     */
    public function downloadMovieAction($slug)
    {
        $item = $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
