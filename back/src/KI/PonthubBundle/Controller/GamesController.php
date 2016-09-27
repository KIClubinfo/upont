<?php

namespace KI\PonthubBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GamesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Game', 'Ponthub');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les jeux",
     *  output="KI\PonthubBundle\Entity\Game",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games")
     * @Method("GET")
     */
    public function getGamesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un jeu",
     *  output="KI\PonthubBundle\Entity\Game",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games/{slug}")
     * @Method("GET")
     */
    public function getGameAction($slug)
    {
        $game = $this->getOne($slug);

        return $this->json($game);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un jeu",
     *  input="KI\PonthubBundle\Form\GameType",
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
     * @Route("/games/{slug}")
     * @Method("PATCH")
     */
    public function patchGameAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

        return $this->formJson($data);
    }


    /**
     * @ApiDoc(
     *  description="Supprime un jeux",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/games/{slug}")
     * @Method("DELETE")
     */
    public function deleteGameAction($slug)
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games/{slug}/download")
     * @Method("GET")
     */
    public function downloadGameAction($slug)
    {
        $item =  $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
