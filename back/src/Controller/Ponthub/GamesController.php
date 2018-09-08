<?php

namespace App\Controller\Ponthub;

use App\Entity\Game;
use App\Form\GameType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GamesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Game::class, GameType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les jeux",
     *  output="App\Entity\Game",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games", methods={"GET"})
     */
    public function getGamesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un jeu",
     *  output="App\Entity\Game",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games/{slug}", methods={"GET"})
     */
    public function getGameAction($slug)
    {
        $game = $this->getOne($slug);

        return $this->json($game);
    }

    /**
     * @ApiDoc(
     *  description="Modifie un jeu",
     *  input="App\Form\GameType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games/{slug}", methods={"PATCH"})
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
     *  },
     *  section="Publications"
     * )
     * @Route("/games/{slug}", methods={"DELETE"})
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
     *  },
     *  section="Ponthub"
     * )
     * @Route("/games/{slug}/download", methods={"GET"})
     */
    public function downloadGameAction($slug)
    {
        $item =  $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
