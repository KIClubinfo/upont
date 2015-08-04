<?php

namespace KI\PonthubBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class SeriesController extends PonthubFileController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
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
     */
    public function getSeriesAction() { return $this->getAll(); }

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
     */
    public function getSerieAction($slug) { return $this->getOne($slug); }

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
     */
    public function patchSerieAction($slug)
    {
        return $this->patch($slug, $this->get('security.context')->isGranted('ROLE_JARDINIER'));
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
     */
    public function getSerieEpisodesAction($slug) { return $this->getAllSub($slug, 'Episode'); }

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
     */
    public function getSerieEpisodeAction($slug, $id) { return $this->getOneSub($slug, 'Episode', $id); }

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
     */
    public function patchSerieEpisodeAction($slug, $id)
    {
        return $this->patchSub($slug, 'Episode', $id, $this->get('security.context')->isGranted('ROLE_JARDINIER'));
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
     * @Route\Get("/series/{slug}/episodes/{id}/download")
     */
    public function downloadEpisodeAction($slug, $id)
    {
        $item = $this->getOneSub($slug, 'Episode', $id);
        return $this->download($item);
    }
}
