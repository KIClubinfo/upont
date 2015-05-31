<?php

namespace KI\UpontBundle\Controller\Ponthub;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\UpontBundle\Entity\Ponthub\Album;
use KI\UpontBundle\Entity\Ponthub\Episode;
use KI\UpontBundle\Entity\Ponthub\Game;
use KI\UpontBundle\Entity\Ponthub\Movie;
use KI\UpontBundle\Entity\Ponthub\Music;
use KI\UpontBundle\Entity\Ponthub\Other;
use KI\UpontBundle\Entity\Ponthub\Serie;
use KI\UpontBundle\Entity\Ponthub\Software;
use KI\UpontBundle\Entity\Ponthub\Genre;

class PonthubController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
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
        if ($token != $this->container->getParameter('fleur_token'))
            return $this->jsonResponse('Vous n\'avez pas le droit de faire ça', 403);

        // On récupère le fichier envoyé
        if (!$request->files->has('filelist'))
            throw new BadRequestHttpException('Aucun fichier envoyé');

        // Quelques variables qui vont servir
        $match = $genres = $series = $albums = $pathsDone = array();
        $path = __DIR__.'/../../../../../web/uploads/tmp/';
        $validExt = array(
            'mp3', 'wav', 'ogg', 'flac', 'mp2', 'aac',
            'avi', 'mpeg', 'mp4', 'mkv',
            'rar', 'zip', 'iso', 'exe', 'msi',
            'jpg', 'jpeg', 'png', 'bmp', 'gif',
            'srt',
            'pdf', 'epub', 'mobi'
        );

        // On récupère le contenu du fichier
        $request->files->get('filelist')->move($path, 'files.list');
        $list = fopen($path.'files.list', 'r+');
        if ($list === false)
            throw new BadRequestHttpException('Erreur lors de l\'upload du fichier');

        // On va modifier les entités en fonction de la liste, on récupère les
        // chemins de toutes les entités Ponthub
        $this->em = $this->getDoctrine()->getManager();
        $repoSeries = $this->em->getRepository('KIUpontBundle:Ponthub\Serie');
        $repoAlbums = $this->em->getRepository('KIUpontBundle:Ponthub\Album');
        $paths = $this->repo->createQueryBuilder('r')->select('r.path')->getQuery()->getScalarResult();
        $paths = array_map('current', $paths);

        // On stocke les albums et les séries existantes
        $result = $repoSeries->findAll();
        foreach ($result as $serie) {
            $series[$serie->getName()] = $serie;
        }
        $result = $repoAlbums->findAll();
        foreach ($result as $album) {
            $albums[$album->getName()] = $album;
        }

        // On liste aussi les genres pour les musiques
        $repoGenres = $this->em->getRepository('KIUpontBundle:Ponthub\Genre');
        $result = $repoGenres->findAll();
        foreach ($result as $genre) {
            $genres[$genre->getName()] = $genre;
        }

        // On parcourt la liste ligne par ligne
        while (!feof($list)) {
            // On enlève le caractère de fin de line
            $line = str_replace(array("\r", "\n"), array('', ''), fgets($list));

            // On récupère la taille du fichier et on l'enlève de la line
            // pour garder uniquement le chemin
            preg_match('#^(([0-9]+)[\t ]*)#', $line, $match);

            // On vérifie que la line a bien la bonne syntaxe, càd
            // %size%          %path%
            if (!(isset($match[1]) && isset($match[2])))
                continue;
            $size = $match[2]*1000;
            $line = str_replace($match[1], '', $line);

            // On exclut tous les fichiers de type non valide
            $name = preg_replace(array('#.*/#', '#\.[a-zA-Z0-9]+$#'), array('', ''), $line);
            $ext = strtolower(substr(strrchr($line, '.'), 1));
            if (!in_array($ext, $validExt))
                continue;

            // On ne crée une nouvelle entrée que si le fichier n'existe pas
            if (in_array($line, $paths)) {
                $pathsDone[] = $line;
                continue;
            }

            // On détermine le dossier dans lequel est rangé le fichier et on range selon le type.
            if (preg_match('#^/root/web/films/#', $line)) {
                if ($size == 0)
                    continue;

                $item = new Movie();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $item->setVo(true);
                $item->setVost(true);
                $item->setHd(true);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/films_light/#', $line)) {
                if ($size == 0)
                    continue;

                $item = new Movie();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $item->setHd(false);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/jeux/#', $line)) {
                if ($size == 0)
                    continue;

                $item = new Game();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/logiciels/#', $line)) {
                if ($size == 0)
                    continue;

                $item = new Software();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/autres/#', $line)) {
                if ($size == 0)
                    continue;

                $item = new Other();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/series/#', $line)) {
                // On détermine les différentes données
                $serie = preg_replace('#/.*#', '', str_replace('/root/web/series/', '', $line));
                $episode = str_replace($ext, '', preg_replace('#.*/#', '', $line));

                // Si la série existe, on la récupère, sinon on la rajoute
                if (!isset($series[$serie])) {
                    $serieItem = new Serie();
                    $serieItem->setAdded(time());
                    $serieItem->setPath('/root/web/series/'.$serie.'/');
                    $serieItem->setStatus('NeedInfos');
                    $serieItem->setName($serie);
                    $serieItem->setVo(true);
                    $serieItem->setHd(false);
                    $this->em->persist($serieItem);
                    $series[$serie] = $serieItem;
                } else
                    $serieItem = $series[$serie];
                if (!in_array('/root/web/series/'.$serie.'/', $pathsDone))
                    $pathsDone[] = '/root/web/series/'.$serie.'/';

                //On range l'épisode en commencant par déterminer le numéro de saison et d'épisode
                if (!preg_match('#^S([0-9]{2}) E([0-9]{2})#', $episode, $matches))
                    continue;

                list(, $numberS, $numberE) = $matches;
                $item = new Episode();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setSeason($numberS);
                $item->setNumber($numberE);
                $item->setStatus('OK');
                $item->setName($name);
                $item->setSerie($serieItem);

                // On actualise la date de modification de la série
                $serieItem->setAdded(time());
                $this->em->persist($item);
                $pathsDone[] = $line;
            }
            if (preg_match('#^/root/web/musiques/#', $line)) {
                // On détermine les différentes données
                $genre = preg_replace('#/.*#', '', str_replace('/root/web/musiques/', '', $line));
                $artist = preg_replace('#/.*#', '', str_replace('/root/web/musiques/'.$genre.'/', '', $line));
                $album = preg_replace('#/.*#', '', str_replace('/root/web/musiques/'.$genre.'/'.$artist.'/', '', $line));

                // Si le genre existe, on le récupère, sinon on le rajoute
                if (!isset($genres[$genre])) {
                    $genreItem = new Genre();
                    $genreItem->setName($genre);
                    $this->em->persist($genreItem);
                    $genres[$genre] = $genreItem;
                } else
                    $genreItem = $genres[$genre];

                // Si l'album existe, on le récupère, sinon on le rajoute
                if (!isset($albums[$album])) {
                    $albumItem = new Album();
                    $albumItem->setAdded(time());
                    $albumItem->setName($album);
                    $albumItem->setArtist($artist);
                    $albumItem->setStatus('NeedInfos');
                    $albumItem->setPath('/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/');
                    $this->em->persist($albumItem);
                    $albums[$album] = $albumItem;
                    $pathsDone[] = '/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/';
                } else
                    $albumItem = $albums[$album];
                if (!in_array('/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/', $pathsDone))
                    $pathsDone[] = '/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/';

                // Maintenant on range la musique
                $item = new Music();
                $item->setSize($size);
                $item->setAdded(time());
                $item->setPath($line);
                $item->setStatus('OK');
                $item->setName($name);
                $item->addGenre($genreItem);
                $item->setAlbum($albumItem);
                $this->em->persist($item);
                $pathsDone[] = $line;
            }
        }
        $this->em->flush();

        // Maintenant on marque les fichiers non trouvés
        $notFound = array_diff($paths, $pathsDone);
        $items = $this->repo->findByPath($notFound);

        foreach ($items as $item) {
            if (get_class($item) != 'KI\UpontBundle\Entity\Ponthub\Album'
             && get_class($item) != 'KI\UpontBundle\Entity\Ponthub\Serie')
                $item->setStatus('NotFound');
        }

        // Si des nouveaux fichiers ont été ajoutés, on notifie les utilisateurs
        $count = count(array_diff($pathsDone, $paths));
        if ($count > 0) {
            $this->notify(
                'notif_ponthub',
                'Ponthub',
                'De nouveaux fichiers sont disponibles sur Ponthub !'
            );
        }
        $this->em->flush();

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
        $gracenote = $this->get('ki_upont.gracenote');
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

        $imdb = $this->get('ki_upont.imdb');
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

        $imdb = $this->get('ki_upont.imdb');
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
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:Users\User');
        $user = $repo->findOneByUsername($slug);

        // On vérifie que la personne a le droit de consulter les stats
        if ($user !== $this->get('security.context')->getToken()->getUser()
            && ($user->getStatsPonthub() === false || $user->getStatsPonthub() === null)
            && !$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->jsonResponse(array('error' => 'Impossible d\'afficher les statistiques PontHub'));
        }

        $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:Ponthub\PonthubFileUser');
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

        // On complète les tableux au fur et à mesure
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
                $hipster += 1;
        }

        // Dans le chart stacké, on met la date actuelle comme point de fin
        $this->updateSeries($timeline, time()*1000, -1);

        return $this->jsonResponse(array(
            'repartition' => $repartition,
            'timeline' => $timeline,
            'totalSize' => $totalSize,
            'totalFiles' => $totalFiles,
            'hipster' => (int) (10 * $hipster / $totalFiles)
        ));
    }

    // Met à jour une série de données pour le graphe de téléchargements cumulés
    private function updateSeries(&$series, $date, $id) {
        foreach ($series as $key => &$value) {
            if ($key != $id)
                $value['data'][] = array($date, $value['data'][count($value['data'])-1][1]);
            else
                $value['data'][] = array($date, $value['data'][count($value['data'])-1][1] + 1);
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
        $dql = 'SELECT IDENTITY(e.user), SUM(f.size) AS compte FROM KI\UpontBundle\Entity\Ponthub\PonthubFileUser e LEFT JOIN e.file f GROUP BY e.user ORDER BY compte DESC';
        $downloaderIds = $this->em->createQuery($dql)
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
            $repo = $this->getDoctrine()->getManager()->getRepository('KIUpontBundle:Ponthub\PonthubFileUser');
            $downloads = $repo->findBy(array('user' => $value[1]));

            $user = $downloads[0]->getUser();
            $downloaderCategories[] = $user->getFirstName().' '.$user->getLastName();
            for ($i = 0; $i < 6; $i++)
                $downloaderSeries[$i]['data'][] = 0;

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
            $total[$key]  = $row['y'];
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
                FROM KI\UpontBundle\Entity\Ponthub\PonthubFileUser e
                LEFT JOIN e.file f LEFT JOIN e.user u
                WHERE u.promo = \'016\' OR u.promo = \'017\' OR u.promo = \'018\'
                GROUP BY mois, u.promo';
        $results = $this->em->createQuery($dql)->getResult();

        $timeline = array(
            'promo016' => array(0,0,0,0,0,0,0,0,0,0,0,0),
            'promo017' => array(0,0,0,0,0,0,0,0,0,0,0,0),
            'promo018' => array(0,0,0,0,0,0,0,0,0,0,0,0),
            'average' => array(0,0,0,0,0,0,0,0,0,0,0,0),
            'pie' => array(
                'promo016' => 0,
                'promo017' => 0,
                'promo018' => 0,
            )
        );
        // On répartit les données dans les tableaux suivants
        foreach ($results as $result) {
            $size = round($result['taille'] / (1000*1000*1000), 1);
            $timeline['promo'.$result['promo']][$result['mois']-1] += $size;
            $timeline['pie']['promo'.$result['promo']] += $size;
        }
        // On calcule les moyennes
        for ($i = 0; $i < 12; $i++) {
            $timeline['average'][$i] = round(($timeline['promo016'][$i] + $timeline['promo017'][$i] + $timeline['promo018'][$i]) / 3, 1);
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
        $dql = 'SELECT e.year, COUNT(e.id) FROM KI\UpontBundle\Entity\Ponthub\Movie e GROUP BY e.year';
        $movieYears = $this->em->createQuery($dql)->getResult();
        $dql = 'SELECT e.year, COUNT(e.id) FROM KI\UpontBundle\Entity\Ponthub\Game e GROUP BY e.year';
        $gameYears = $this->em->createQuery($dql)->getResult();

        $yearCategories = array();
        $yearSeries = array(
            array('name' => 'Films', 'data' => array()),
            array('name' => 'Jeux', 'data' => array())
        );

        // On rajoute l'année dans les catégories si elle n'y est pas déjà
        foreach ($movieYears as $key => $value) {
            if (!in_array((int) $value['year'], $yearCategories)) {
                $yearCategories[] = $value['year'];
                $yearSeries[0]['data'][] = 0;
                $yearSeries[1]['data'][] = 0;
            }
        }
        foreach ($gameYears as $key => $value) {
            if (!in_array((int) $value['year'], $yearCategories)) {
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
            $id = array_search((int) $value['year'], $yearCategories);
            $yearSeries[0]['data'][$id] = -$value[1];

            if ($value[1] > $maxPopMovie)
                $maxPopMovie = (int) $value[1];
        }
        foreach ($gameYears as $key => $value) {
            $id = array_search((int) $value['year'], $yearCategories);
            $yearSeries[1]['data'][$id] = (int) $value[1];

            if ($value[1] > $maxPopGame)
                $maxPopGame = (int) $value[1];
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
            $dql = 'SELECT SUM(e.size) FROM KI\UpontBundle\Entity\Ponthub\\'.$category.' e';
            return $this->em->createQuery($dql)->getSingleScalarResult() / (1000*1000*1000);
        } else {
            $dql = 'SELECT COUNT(e.id) FROM KI\UpontBundle\Entity\Ponthub\\'.$category.' e';
            return $this->em->createQuery($dql)->getSingleScalarResult();
        }
    }

    private function getDownloads($category) {
        $connection = $this->em->getConnection();
        $statement = $connection->prepare('SELECT Likeable.name, COUNT(*) AS compte FROM PonthubFileUser LEFT JOIN Likeable ON Likeable.id = PonthubFileUser.file_id WHERE Likeable.type = :category GROUP BY PonthubFileUser.file_id ORDER BY compte DESC LIMIT 10');
        $statement->bindValue('category', $category);
        $statement->execute();
        $results = $statement->fetchAll();

        $return = array();
        foreach ($results as $result) {
            $return[] = array($result['name'], (int) $result['compte']);
        }
        return $return;
    }

    private function getTotalDownloads($category) {
        $connection = $this->em->getConnection();
        $statement = $connection->prepare('SELECT COUNT(*) AS compte FROM PonthubFileUser LEFT JOIN Likeable ON Likeable.id = PonthubFileUser.file_id WHERE Likeable.type = :category');
        $statement->bindValue('category', $category);
        $statement->execute();
        $results = $statement->fetchAll();

        return (int) $results[0]['compte'];
    }
}
