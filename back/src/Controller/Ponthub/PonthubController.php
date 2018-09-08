<?php

namespace App\Controller\Ponthub;

use App\Controller\ResourceController;
use App\Entity\PonthubFile;
use App\Entity\User;
use App\Helper\FilelistHelper;
use App\Helper\GlobalStatisticsHelper;
use App\Helper\StatisticsHelper;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PonthubController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(PonthubFile::class, null);
    }

    /**
     * @ApiDoc(
     *  description="Actualise la base de données à partir de la liste des fichiers sur Fleur",
     *  requirements={
     *   {
     *    "name"="filelist",
     *    "dataType"="file",
     *    "description"="La liste des fichiers sur fleur au formmat : %size% %full_path%"
     *   }
     *  },
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
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
     * @ApiDoc(
     *  description="Recherche des films/séries sur Imdb",
     *  requirements={
     *   {
     *    "name"="name",
     *    "dataType"="string",
     *    "description"="Le critère de recherche"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/imdb/search", methods={"POST"})
     */
    public function imdbSearchAction(Request $request)
    {
        $this->trust($this->is('USER'));

        if (!$request->request->has('name')) {
            throw new BadRequestHttpException();
        }

        $imdb = $this->get('ki_ponthub.service.imdb');
        $infos = $imdb->search($request->request->get('name'));

        return $this->json($infos, 200);
    }

    /**
     * @ApiDoc(
     *  description="Retourne les informations sur un film/une série d'Imdb",
     *  requirements={
     *   {
     *    "name"="id",
     *    "dataType"="string",
     *    "description"="L'identifiant de la ressource Imdb"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Ponthub"
     * )
     * @Route("/imdb/infos", methods={"POST"})
     */
    public function imdbInfosAction(Request $request)
    {
        $this->trust($this->is('USER'));

        if (!$request->request->has('id')) {
            throw new BadRequestHttpException();
        }

        $imdb = $this->get('ki_ponthub.service.imdb');
        $infos = $imdb->infos($request->request->get('id'));

        if ($infos === null) {
            throw new NotFoundHttpException('Ce film/cette série n\'existe pas dans la base Imdb');
        }

        return $this->json($infos, 200);
    }

    /**
     * @ApiDoc(
     *  description="Retourne les statistiques d'utilisation de Ponthub pour un utilisateur particulier",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
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
     * @ApiDoc(
     *  description="Retourne les statistiques d'utilisation de Ponthub",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Ponthub"
     * )
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
