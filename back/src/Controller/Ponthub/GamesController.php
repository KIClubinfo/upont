<?php

namespace App\Controller\Ponthub;

use App\Entity\Game;
use App\Form\GameType;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class GamesController extends PonthubFileController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Game::class, GameType::class);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Liste les jeux",
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
     * @Route("/games", methods={"GET"})
     */
    public function getGamesAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne un jeu",
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
     * @Route("/games/{slug}", methods={"GET"})
     */
    public function getGameAction($slug)
    {
        $game = $this->getOne($slug);

        return $this->json($game);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Modifie un jeu",
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
     *         name="year",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="studio",
     *         in="body",
     *         description="",
     *         required=false,
     *         type="string",
     *         schema=""
     *     ),
     *     @SWG\Parameter(
     *         name="os",
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
     * @Route("/games/{slug}", methods={"PATCH"})
     */
    public function patchGameAction($slug)
    {
        $data = $this->patch($slug, $this->is('JARDINIER'));

        return $this->formJson($data);
    }


    /**
     * @Operation(
     *     tags={"Publications"},
     *     summary="Supprime un jeux",
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
     * @Route("/games/{slug}", methods={"DELETE"})
     */
    public function deleteGameAction($slug)
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
     * @Route("/games/{slug}/download", methods={"GET"})
     */
    public function downloadGameAction($slug)
    {
        $item = $this->getOne($slug, !$this->is('EXTERIEUR'));
        return $this->download($item);
    }
}
