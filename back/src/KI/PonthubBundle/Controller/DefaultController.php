<?php

namespace KI\PonthubBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\CoreBundle\Controller\ResourceController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DefaultController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('PonthubFile', 'Ponthub');
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function filelistAction($token, Request $request)
    {
        $path = __DIR__.'/../../../../web/uploads/tmp/';
        if ($token != $this->container->getParameter('fleur_token')) {
            return $this->jsonResponse('Vous n\'avez pas le droit de faire ça', 403);
        }

        // On récupère le fichier envoyé
        if (!$request->files->has('filelist')) {
            throw new BadRequestHttpException('Aucun fichier envoyé');
        }

        // On récupère le contenu du fichier
        $request->files->get('filelist')->move($path, 'files.list');
        $list = fopen($path.'files.list', 'r+');
        if ($list === false) {
            throw new BadRequestHttpException('Erreur lors de l\'upload du fichier');
        }

        $filelistHelper = $this->get('ki_ponthub.helper.filelist');
        $filelistHelper->parseFilelist($list);

        return $this->jsonResponse(null, 202);
    }

    /**
     * @ApiDoc(
     *  description="Récupère des informations sur un album de musique grâce à l'API Gracenote",
     *  requirements={
     *   {
     *    "name"="album",
     *    "dataType"="string",
     *    "description"="Le critère de recherche est le nom de l'album"
     *   }
     *  },
     *  parameters={
     *   {
     *    "name"="artist",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Le nom de l'artiste peut être spécifié pour maximiser la pertinence"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function gracenoteAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        if (!$request->request->has('album'))
            throw new BadRequestHttpException();

        $album = $request->request->get('album');
        $artist = $request->request->has('artist') ? $request->request->get('artist') : '';
        $gracenote = $this->get('ki_ponthub.service.gracenote');
        $infos = $gracenote->searchAlbum($album, $artist);

        $response = new Response();
        $response->setContent(json_encode($infos));
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function imdbSearchAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        if (!$request->request->has('name'))
            throw new BadRequestHttpException();

        $imdb = $this->get('ki_ponthub.service.imdb');
        $infos = $imdb->search($request->request->get('name'));

        $response = new Response();
        $response->setContent(json_encode($infos));
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
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
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     * @return array
     */
    public function imdbInfosAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();

        if (!$request->request->has('id'))
            throw new BadRequestHttpException();

        $imdb = $this->get('ki_ponthub.service.imdb');
        $infos = $imdb->infos($request->request->get('id'));

        if ($infos === null)
            throw new NotFoundHttpException('Ce film/cette série n\'existe pas dans la base Imdb');

        $response = new Response();
        $response->setContent(json_encode($infos));
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @ApiDoc(
     *  description="Retourne les statistiques d'utilisation de Ponthub pour un utilisateur particulier",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function statisticsAction($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($slug);

        // On vérifie que la personne a le droit de consulter les stats
        if ($user !== $this->get('security.context')->getToken()->getUser()
            && ($user->getStatsPonthub() === false || $user->getStatsPonthub() === null)
            && !$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->jsonResponse(array('error' => 'Impossible d\'afficher les statistiques PontHub'));
        }

        $repo = $this->getDoctrine()->getManager()->getRepository('KIPonthubBundle:PonthubFileUser');
        $downloads = $repo->findBy(array('user' => $user), array('date' => 'ASC'));
        $totalFiles = count($downloads);

        if ($totalFiles == 0)
            return $this->jsonResponse(array('repartition' => array(), 'timeline' => array(), 'totalSize' => 0, 'totalFiles' => 0, 'hipster' => 0));

        // Initialisation des séries
        $repartition = array(array('Films', 0), array('Épisodes', 0), array('Musiques', 0), array('Jeux', 0), array('Logiciels', 0), array('Autres', 0));
        $date = 1000*($downloads[0]->getDate() - 10*3600);
        $timeline = array(
            array('name' => 'Films', 'data' => array(array($date, 0))),
            array('name' => 'Épisodes', 'data' => array(array($date, 0))),
            array('name' => 'Musiques', 'data' => array(array($date, 0))),
            array('name' => 'Jeux', 'data' => array(array($date, 0))),
            array('name' => 'Logiciels', 'data' => array(array($date, 0))),
            array('name' => 'Autres', 'data' => array(array($date, 0)))
        );
        $totalSize = 0;
        $hipster = 0;

        // On complète les tableaux au fur et à mesure
        foreach ($downloads as $download) {
            $file = $download->getFile();
            // Conversion en millisecondes, unité javascript de base
            $date = $download->getDate()*1000;

            if ($file instanceof Movie) {
                $repartition[0][1]++;
                $this->updateSeries($timeline, $date, 0);
            }
            if ($file instanceof Episode) {
                $repartition[1][1]++;
                $this->updateSeries($timeline, $date, 1);
            }
            if ($file instanceof Music) {
                $repartition[2][1]++;
                $this->updateSeries($timeline, $date, 2);
            }
            if ($file instanceof Game) {
                $repartition[3][1]++;
                $this->updateSeries($timeline, $date, 3);
            }
            if ($file instanceof Software) {
                $repartition[4][1]++;
                $this->updateSeries($timeline, $date, 4);
            }
            if ($file instanceof Other) {
                $repartition[5][1]++;
                $this->updateSeries($timeline, $date, 5);
            }
            $totalSize += $file->getSize();

            // Gain de points hipsteritude en fonction du nombre d'autres
            // personnes qui ont téléchargé le fichier
            $c = $file->downloads() - 1;
            if ($c == 0)
                $hipster += 20;
            else if ($c < 2)
                $hipster += 15;
            else if ($c < 4)
                $hipster += 10;
            else if ($c < 9)
                $hipster += 5;
            else if ($c < 19)
                $hipster += 3;
            else
                $hipster++;
        }

        // Dans le chart stacké, on met la date actuelle comme point de fin
        $this->updateSeries($timeline, time()*1000, -1);

        return $this->jsonResponse(array(
            'repartition' => $repartition,
            'timeline' => $timeline,
            'totalSize' => $totalSize,
            'totalFiles' => $totalFiles,
            'hipster' => (int)(10*$hipster/$totalFiles)
        ));
    }

    // Met à jour une série de données pour le graphe de téléchargements cumulés
    private function updateSeries(&$series, $date, $id) {
        foreach ($series as $key => &$value) {
            if ($key != $id)
                $value['data'][] = array($date, $value['data'][count($value['data']) - 1][1]);
            else
                $value['data'][] = array($date, $value['data'][count($value['data']) - 1][1] + 1);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retourne les statistiques d'utilisation de Ponthub",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Ponthub"
     * )
     */
    public function statisticsMainAction()
    {
        // Recherche des plus gros downloaders
        $dql = 'SELECT IDENTITY(e.user), SUM(f.size) AS compte FROM KI\PonthubBundle\Entity\PonthubFileUser e LEFT JOIN e.file f GROUP BY e.user ORDER BY compte DESC';
        $downloaderIds = $this->manager->createQuery($dql)
                                ->setMaxResults(10)
                                ->getResult();

        // On regarde les détails sur chaque utilisateur
        $downloaderCategories = array();
        $downloaderSeries = array(
            array('name' => 'Films', 'data' => array()),
            array('name' => 'Séries', 'data' => array()),
            array('name' => 'Musiques', 'data' => array()),
            array('name' => 'Jeux', 'data' => array()),
            array('name' => 'Logiciels', 'data' => array()),
            array('name' => 'Autres', 'data' => array())
        );
        foreach ($downloaderIds as $key => $value) {
            $repo = $this->getDoctrine()->getManager()->getRepository('KIPonthubBundle:PonthubFileUser');
            $downloads = $repo->findBy(array('user' => $value[1]));

            $user = $downloads[0]->getUser();
            $downloaderCategories[] = $user->getFirstName().' '.$user->getLastName();
            for ($i = 0; $i < 6; $i++) {
                            $downloaderSeries[$i]['data'][] = 0;
            }

            foreach ($downloads as $download) {
                $file = $download->getFile();
                if ($file instanceof Movie)
                    $downloaderSeries[0]['data'][$key] += round($file->getSize()/(1000*1000*1000), 1);
                if ($file instanceof Episode)
                    $downloaderSeries[1]['data'][$key] += round($file->getSize()/(1000*1000*1000), 1);
                if ($file instanceof Music)
                    $downloaderSeries[2]['data'][$key] += round($file->getSize()/(1000*1000*1000), 1);
                if ($file instanceof Game)
                    $downloaderSeries[3]['data'][$key] += round($file->getSize()/(1000*1000*1000), 1);
                if ($file instanceof Software)
                    $downloaderSeries[4]['data'][$key] += round($file->getSize()/(1000*1000*1000), 1);
                if ($file instanceof Other)
                    $downloaderSeries[5]['data'][$key] += round($file->getSize()/(1000*1000*1000), 1);
            }
        }

        // Recherche des fichiers les plus téléchargés
        $downloadSerie = array(
            array('name' => 'Films', 'drilldown' => 1, 'y' => $this->getTotalDownloads('movie')),
            array('name' => 'Séries', 'drilldown' => 2, 'y' => $this->getTotalDownloads('episode')),
            array('name' => 'Musiques', 'drilldown' => 3, 'y' => $this->getTotalDownloads('music')),
            array('name' => 'Jeux', 'drilldown' => 4, 'y' => $this->getTotalDownloads('game')),
            array('name' => 'Logiciels', 'drilldown' => 5, 'y' => $this->getTotalDownloads('software')),
            array('name' => 'Autres', 'drilldown' => 6, 'y' => $this->getTotalDownloads('other'))
        );

        // Tri par ordre des downloads totaux
        $total = array();
        foreach ($downloadSerie as $key => $row) {
            $total[$key] = $row['y'];
        }
        array_multisort($total, SORT_DESC, $downloadSerie);

        $downloadDrilldown = array(
            array('name' => 'Films', 'id' => 1, 'data' => $this->getDownloads('movie')),
            array('name' => 'Séries', 'id' => 2, 'data' => $this->getDownloads('episode')),
            array('name' => 'Musiques', 'id' => 3, 'data' => $this->getDownloads('music')),
            array('name' => 'Jeux', 'id' => 4, 'data' => $this->getDownloads('game')),
            array('name' => 'Logiciels', 'id' => 5, 'data' => $this->getDownloads('software')),
            array('name' => 'Autres', 'id' => 6, 'data' => $this->getDownloads('other'))
        );

        // Timeline répartition par promos par mois
        $dql = 'SELECT u.promo, MONTH(e.date) AS mois, SUM(f.size) AS taille
                FROM KI\PonthubBundle\Entity\PonthubFileUser e
                LEFT JOIN e.file f LEFT JOIN e.user u
                WHERE u.promo = \'016\' OR u.promo = \'017\' OR u.promo = \'018\'
                GROUP BY mois, u.promo';
        $results = $this->manager->createQuery($dql)->getResult();

        $timeline = array(
            'promo016' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            'promo017' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            'promo018' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            'average' => array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
            'pie' => array(
                'promo016' => 0,
                'promo017' => 0,
                'promo018' => 0,
            )
        );
        // On répartit les données dans les tableaux suivants
        foreach ($results as $result) {
            $size = round($result['taille']/(1000*1000*1000), 1);
            $timeline['promo'.$result['promo']][$result['mois'] - 1] += $size;
            $timeline['pie']['promo'.$result['promo']] += $size;
        }
        // On calcule les moyennes
        for ($i = 0; $i < 12; $i++) {
            $timeline['average'][$i] = round(($timeline['promo016'][$i] + $timeline['promo017'][$i] + $timeline['promo018'][$i])/3, 1);
        }

        // Construction de la tree map résumant les fichiers dispos sur Ponthub
        $ponthub = array(
            'Nombre de fichiers dispos' => array(
                'Films' => $this->getTotal('Movie'),
                'Séries' => $this->getTotal('Episode'),
                'Musiques' => $this->getTotal('Music'),
                'Jeux' => $this->getTotal('Game'),
                'Logiciels' => $this->getTotal('Software'),
                'Autres' => $this->getTotal('Other')
            ),
            'Volume de fichiers (Go)' => array(
                'Films' => $this->getTotal('Movie', true),
                'Séries' => $this->getTotal('Episode', true),
                'Musiques' => $this->getTotal('Music', true),
                'Jeux' => $this->getTotal('Game', true),
                'Logiciels' => $this->getTotal('Software', true),
                'Autres' => $this->getTotal('Other', true)
            )
        );

        // Construction de l'arbre des années des films/jeux dispos
        $dql = 'SELECT e.year, COUNT(e.id) FROM KI\PonthubBundle\Entity\Movie e GROUP BY e.year';
        $movieYears = $this->manager->createQuery($dql)->getResult();
        $dql = 'SELECT e.year, COUNT(e.id) FROM KI\PonthubBundle\Entity\Game e GROUP BY e.year';
        $gameYears = $this->manager->createQuery($dql)->getResult();

        $yearCategories = array();
        $yearSeries = array(
            array('name' => 'Films', 'data' => array()),
            array('name' => 'Jeux', 'data' => array())
        );

        // On rajoute l'année dans les catégories si elle n'y est pas déjà
        foreach ($movieYears as $key => $value) {
            if (!in_array((int)$value['year'], $yearCategories)) {
                $yearCategories[] = $value['year'];
                $yearSeries[0]['data'][] = 0;
                $yearSeries[1]['data'][] = 0;
            }
        }
        foreach ($gameYears as $key => $value) {
            if (!in_array((int)$value['year'], $yearCategories)) {
                $yearCategories[] = $value['year'];
                $yearSeries[0]['data'][] = 0;
                $yearSeries[1]['data'][] = 0;
            }
        }

        // On ordonne les années
        sort($yearCategories);

        // On répartit les entrées
        $maxPopMovie = 0;
        $maxPopGame = 0;
        foreach ($movieYears as $key => $value) {
            $id = array_search((int)$value['year'], $yearCategories);
            $yearSeries[0]['data'][$id] = -$value[1];

            if ($value[1] > $maxPopMovie)
                $maxPopMovie = (int)$value[1];
        }
        foreach ($gameYears as $key => $value) {
            $id = array_search((int)$value['year'], $yearCategories);
            $yearSeries[1]['data'][$id] = (int)$value[1];

            if ($value[1] > $maxPopGame)
                $maxPopGame = (int)$value[1];
        }

        return $this->jsonResponse(array(
            'downloaders' => array(
                'categories' => $downloaderCategories,
                'series' => $downloaderSeries
            ),
            'downloads' => array(
                'serie' => $downloadSerie,
                'drilldown' => $downloadDrilldown
            ),
            'ponthub' => $ponthub,
            'years' => array(
                'categories' => $yearCategories,
                'series' => $yearSeries,
                'min' => -$maxPopMovie,
                'max' => $maxPopGame
            ),
            'timeline' => $timeline
        ));
    }

    // Retourne les données selon une catégorie de fichiers
    private function getTotal($category, $size = false) {
        if ($size) {
            $dql = 'SELECT SUM(e.size) FROM KI\PonthubBundle\Entity\\'.$category.' e';
            return $this->manager->createQuery($dql)->getSingleScalarResult()/(1000*1000*1000);
        } else {
            $dql = 'SELECT COUNT(e.id) FROM KI\PonthubBundle\Entity\\'.$category.' e';
            return $this->manager->createQuery($dql)->getSingleScalarResult();
        }
    }

    private function getDownloads($category) {
        $connection = $this->manager->getConnection();
        $statement = $connection->prepare('SELECT Likeable.name, COUNT(*) AS compte FROM PonthubFileUser LEFT JOIN Likeable ON Likeable.id = PonthubFileUser.file_id WHERE Likeable.type = :category GROUP BY PonthubFileUser.file_id ORDER BY compte DESC LIMIT 10');
        $statement->bindValue('category', $category);
        $statement->execute();
        $results = $statement->fetchAll();

        $return = array();
        foreach ($results as $result) {
            $return[] = array($result['name'], (int)$result['compte']);
        }
        return $return;
    }

    private function getTotalDownloads($category) {
        $connection = $this->manager->getConnection();
        $statement = $connection->prepare('SELECT COUNT(*) AS compte FROM PonthubFileUser LEFT JOIN Likeable ON Likeable.id = PonthubFileUser.file_id WHERE Likeable.type = :category');
        $statement->bindValue('category', $category);
        $statement->execute();
        $results = $statement->fetchAll();

        return (int)$results[0]['compte'];
    }
}
