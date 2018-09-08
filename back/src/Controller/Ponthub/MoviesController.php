<?php

namespace App\Controller\Ponthub;

use App\Entity\Movie;
use App\Form\MovieType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MoviesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Movie::class, MovieType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les films",
     *  output="App\Entity\Movie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/movies", methods={"GET"})
     */
    public function getMoviesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un film",
     *  output="App\Entity\Movie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/movies/{slug}", methods={"GET"})
     */
    public function getMovieAction($slug)
    {
        $movie = $this->getOne($slug);

        return $this->json($movie);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un film",
     *  input="App\Form\MovieType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/movies/{slug}", methods={"PATCH"})
     */
    public function patchMovieAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime un film",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/movies/{slug}", methods={"DELETE"})
     */
    public function deleteMovieAction($slug)
    {
        $this->delete($slug, $this->is('JARDINIER'));

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Télécharge un fichier sur Ponthub, et log le téléchargement",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/movies/{slug}/download", methods={"GET"})
     */
    public function downloadMovieAction($slug)
    {
        $item = $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
