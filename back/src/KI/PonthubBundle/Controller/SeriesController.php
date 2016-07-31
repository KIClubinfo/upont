<?php

namespace KI\PonthubBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeriesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Serie', 'Ponthub');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les séries",
     *  output="KI\PonthubBundle\Entity\Serie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series")
     * @Method("GET")
     */
    public function getSeriesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne une série",
     *  output="KI\PonthubBundle\Entity\Serie",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}")
     * @Method("GET")
     */
    public function getSerieAction($slug)
    {
        $serie = $this->getOne($slug);

        return $this->json($serie);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une série",
     *  input="KI\PonthubBundle\Form\SerieType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}")
     * @Method("PATCH")
     */
    public function patchSerieAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/series/{slug}")
     * @Method("DELETE")
     */
    public function deleteSerieAction($slug)
    {
        $this->delete($slug, $this->is('JARDINIER'));

        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Liste les épisodes d'une série",
     *  output="KI\PonthubBundle\Entity\Episode",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes")
     * @Method("GET")
     */
    public function getSerieEpisodesAction($slug)
    {
        $episodes =  $this->getAllSub($slug, 'Episode');

        return $this->json($episodes);
    }

    /**
     * @ApiDoc(
     *  description="Retourne un épisode d'une série",
     *  output="KI\PonthubBundle\Entity\Episode",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes/{id}")
     * @Method("GET")
     */
    public function getSerieEpisodeAction($slug, $id)
    {
        $episode =  $this->getOneSub($slug, 'Episode', $id);

        return $this->json($episode);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un épisode d'une série",
     *  input="KI\PonthubBundle\Form\EpisodeType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes/{id}")
     * @Method("PATCH")
     */
    public function patchSerieEpisodeAction($slug, $id)
    {
        $episode = $this->patchSub($slug, 'Episode', $id, $this->is('JARDINIER'));

        return $this->json($episode, 204);
    }

    /**
     * @ApiDoc(
     *  description="Télécharge un fichier sur Ponthub, et log le téléchargement",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/series/{slug}/episodes/{id}/download")
     * @Method("GET")
     */
    public function downloadEpisodeAction($slug, $id)
    {
        $episode = $this->getOneSub($slug, 'Episode', $id);

        return $this->download($episode);
    }
}
