<?php

namespace App\Controller\Ponthub;

use App\Entity\Episode;
use App\Entity\Serie;
use App\Form\SerieType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeriesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Serie::class, SerieType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les séries",
     *  output="App\Entity\Serie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series", methods={"GET"})
     */
    public function getSeriesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne une série",
     *  output="App\Entity\Serie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}", methods={"GET"})
     */
    public function getSerieAction(Serie $serie)
    {
        return $this->json($serie);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une série",
     *  input="App\Form\SerieType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}", methods={"PATCH"})
     */
    public function patchSerieAction(Serie $serie)
    {
        $data = $this->patchItem($serie, $this->is('JARDINIER'));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une série",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Publications"
     * )
     * @Route("/series/{slug}", methods={"DELETE"})
     */
    public function deleteSerieAction(Serie $serie)
    {
        $this->deleteItem($serie, $this->is('JARDINIER'));

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Liste les épisodes d'une série",
     *  output="App\Entity\Episode",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes", methods={"GET"})
     */
    public function getSerieEpisodesAction(Serie $serie)
    {
        $episodes = $serie->getEpisodes();

        return $this->json($episodes);
    }

    /**
     * @ApiDoc(
     *  description="Retourne un épisode d'une série",
     *  output="App\Entity\Episode",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes/{episode_slug}", methods={"GET"})
     * @ParamConverter("episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function getSerieEpisodeAction(Serie $serie, Episode $episode)
    {
        return $this->json($episode);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un épisode d'une série",
     *  input="App\Form\EpisodeType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes/{episode_slug}", methods={"PATCH"})
     * @ParamConverter("episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function patchSerieEpisodeAction(Serie $serie, Episode $episode)
    {
        $data = $this->patchItem($episode, $this->is('JARDINIER'));

        return $this->formJson($data);
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
     * @Route("/series/{slug}/episodes/{episode_slug}/download", methods={"GET"})
     * @ParamConverter("episode", options={"mapping": {"episode_slug": "slug"}})
     */
    public function downloadEpisodeAction(Serie $serie, Episode $episode)
    {
        return $this->download($episode);
    }
}
