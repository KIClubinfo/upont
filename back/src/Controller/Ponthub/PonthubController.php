<?php

namespace App\Controller\Ponthub;

use App\Controller\ResourceController;
use App\Entity\PonthubFile;
use App\Entity\User;
use App\Helper\FilelistHelper;
use App\Helper\GlobalStatisticsHelper;
use App\Helper\StatisticsHelper;
use App\Service\ImdbService;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PonthubController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(PonthubFile::class, null);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Actualise la base de données à partir de la liste des fichiers sur Fleur",
     *     @SWG\Response(
     *         response="202",
     *         description="Requête traitée mais sans garantie de résultat"
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
     * @Route("/filelist/{token}", methods={"POST"})
     */
    public function filelistAction(FilelistHelper $filelistHelper, Request $request, $token)
    {
        $path = __DIR__ . '/../../../public/uploads/tmp/';
        if ($token != $this->container->getParameter('fleur_token')) {
            return $this->json('Vous n\'avez pas le droit de faire ça', 403);
        }

        // On récupère le fichier envoyé
        if (!$request->files->has('filelist')) {
            throw new BadRequestHttpException('Aucun fichier envoyé');
        }

        // On récupère le contenu du fichier
        $request->files->get('filelist')->move($path, 'files.list');
        $list = fopen($path . 'files.list', 'r+');
        if ($list === false) {
            throw new BadRequestHttpException('Erreur lors de l\'upload du fichier');
        }

        $filelistHelper->parseFilelist($list);

        return $this->json(null, 202);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Recherche des films/séries sur Imdb",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
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
     * @Route("/imdb/search", methods={"POST"})
     */
    public function imdbSearchAction(Request $request, ImdbService $imdbService)
    {
        $this->trust($this->is('USER'));

        if (!$request->request->has('name')) {
            throw new BadRequestHttpException();
        }

        $infos = $imdbService->search($request->request->get('name'));

        return $this->json($infos, 200);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne les informations sur un film/une série d'Imdb",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
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
     * @Route("/imdb/infos", methods={"POST"})
     */
    public function imdbInfosAction(Request $request, ImdbService $imdbService)
    {
        $this->trust($this->is('USER'));

        if (!$request->request->has('id')) {
            throw new BadRequestHttpException();
        }

        $infos = $imdbService->infos($request->request->get('id'));

        if ($infos === null) {
            throw new NotFoundHttpException('Ce film/cette série n\'existe pas dans la base Imdb');
        }

        return $this->json($infos, 200);
    }

    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne les statistiques d'utilisation de Ponthub pour un utilisateur particulier",
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
     * @Route("/statistics/ponthub/{slug}", methods={"GET"})
     */
    public function getPonthubStatisticsAction($slug, StatisticsHelper $statisticsHelper)
    {
        $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $userRepository->findOneByUsername($slug);

        // On vérifie que la personne a le droit de consulter les stats
        if ($user !== $this->user && empty($user->getStatsPonthub()) && !$this->is('ADMIN')) {
            return $this->json(['error' => 'Impossible d\'afficher les statistiques PontHub']);
        }

        return $this->json($statisticsHelper->getUserStatistics($user));
    }


    /**
     * @Operation(
     *     tags={"Ponthub"},
     *     summary="Retourne les statistiques d'utilisation de Ponthub",
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
     * @Route("/statistics/ponthub", methods={"GET"})
     */
    public function getPonthubStatisticsMainAction(GlobalStatisticsHelper $statisticsHelper)
    {
        return $this->json([
            'downloaders' => $statisticsHelper->getGlobalDownloaders(),
            'downloads' => $statisticsHelper->getGlobalDownloads(),
            'ponthub' => $statisticsHelper->getGlobalPonthub(),
            'years' => $statisticsHelper->getGlobalYears(),
            'timeline' => $statisticsHelper->getGlobalTimeline()
        ]);
    }
}
