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
    public function filelistAction(Request $request)
    {
        // On récupère le fichier envoyé
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException();

        if (!$request->files->has('filelist'))
            throw new BadRequestHttpException();

        // Quelques variables qui vont servir
        $match = $genres = $series = $albums = $pathsDone = array();
        $path = __DIR__ . '/../../../../../web/uploads/files/';
        $validExt = array(
            'mp3', 'wav', 'ogg', 'flac', 'mp2', 'aac',
            'avi', 'mpeg', 'mp4', 'mkv',
            'rar', 'iso', 'exe', 'msi',
            'jpg', 'jpeg', 'png', 'bmp', 'gif'
        );

        // On récupère le contenu du fichier
        $request->files->get('filelist')->move($path);
        $list = fopen($path . 'files.list', 'r+');
        if ($list === false)
            throw new BadRequestHttpException();

        // On va modifier les entités en fonction de la liste, on récupère les
        // chemins de toutes les entités Ponthub
        $this->em = $this->getDoctrine()->getManager();
        $repoSeries = $this->em->getRepository('KIUpontBundle:Ponthub\Serie');
        $repoAlbums = $this->em->getRepository('KIUpontBundle:Ponthub\Album');
        $paths = $this->repo->createQueryBuilder('r')->select('r.path')->getQuery()->getScalarResult();;
        $paths = array_map('current', $paths);;

        // On stocke les albums et les séries existantes
        $result = $repoSeries->findAll();
        foreach($result as $serie)
            $series[$serie->getName()] = $serie;
        $result = $repoAlbums->findAll();
        foreach($result as $album)
            $albums[$album->getName()] = $album;

        // On liste aussi les genres pour les musiques
        $repoGenres = $this->em->getRepository('KIUpontBundle:Ponthub\Genre');
        $result = $repoGenres->findAll();
        foreach($result as $genre)
            $genres[$genre->getName()] = $genre;

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
                $item = new Movie();
                $item->setSize($size);
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $item->setVo(true);
                $item->setVost(true);
                $item->setHd(true);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/films_light/#', $line)) {
                $item = new Movie();
                $item->setSize($size);
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $item->setHd(false);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/jeux/#', $line)) {
                $item = new Game();
                $item->setSize($size);
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/logiciels/#', $line)) {
                $item = new Software();
                $item->setSize($size);
                $item->setPath($line);
                $item->setStatus('NeedInfos');
                $item->setName($name);
                $this->em->persist($item);
            }
            if (preg_match('#^/root/web/autres/#', $line)) {
                $item = new Other();
                $item->setSize($size);
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
                    $serieItem->setPath('/root/web/series/' . $serie . '/');
                    $serieItem->setStatus('NeedInfos');
                    $serieItem->setName($serie);
                    $serieItem->setVo(true);
                    $serieItem->setHd(false);
                    $this->em->persist($serieItem);
                    $this->em->flush();
                    $series[$serie] = $serieItem;
                }
                else
                    $serieItem = $series[$serie];
                if (!in_array('/root/web/series/' . $serie . '/', $pathsDone))
                    $pathsDone[] = '/root/web/series/' . $serie . '/';

                //On range l'épisode en commencant par déterminer le numéro de saison et d'épisode
                if (!preg_match('#^S([0-9]{2}) E([0-9]{2})#', $episode, $matches))
                    continue;

                list(, $numberS, $numberE) = $matches;
                $item = new Episode();
                $item->setSize($size);
                $item->setPath($line);
                $item->setSeason($numberS);
                $item->setNumber($numberE);
                $item->setStatus('OK');
                $item->setName($name);
                $item->setSerie($serieItem);
                $this->em->persist($item);
                $pathsDone[] = $line;
            }
            if (preg_match('#^/root/web/musiques/#', $line)) {
                // On détermine les différentes données
                $genre = preg_replace('#/.*#', '', str_replace('/root/web/musiques/', '', $line));
                $artist = preg_replace('#/.*#', '', str_replace('/root/web/musiques/' . $genre . '/', '', $line));
                $album = preg_replace('#/.*#', '', str_replace('/root/web/musiques/' . $genre . '/' . $artist . '/', '', $line));

                // Si le genre existe, on le récupère, sinon on le rajoute
                if (!isset($genres[$genre])) {
                    $genreItem = new Genre();
                    $genreItem->setName($genre);
                    $this->em->persist($genreItem);
                    $this->em->flush();
                    $genres[$genre] = $genreItem;
                }
                else
                    $genreItem = $genres[$genre];

                // Si l'album existe, on le récupère, sinon on le rajoute
                if (!isset($albums[$album])) {
                    $albumItem = new Album();
                    $albumItem->setName($album);
                    $albumItem->setArtist($artist);
                    $albumItem->setStatus('NeedInfos');
                    $albumItem->setPath('/root/web/musiques/' . $genre .'/' . $artist . '/' . $album . '/');
                    $this->em->persist($albumItem);
                    $this->em->flush();
                    $albums[$album] = $albumItem;
                    $pathsDone[] = '/root/web/musiques/' . $genre .'/' . $artist . '/' . $album . '/';
                }
                else
                    $albumItem = $albums[$album];
                if (!in_array('/root/web/musiques/' . $genre .'/' . $artist . '/' . $album . '/', $pathsDone))
                    $pathsDone[] = '/root/web/musiques/' . $genre .'/' . $artist . '/' . $album . '/';

                // Maintenant on range la musique
                $item = new Music();
                $item->setSize($size);
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

        foreach($items as $item) {
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
     *  output="KI\UpontBundle\Entity\Dummy\GracenoteResponse",
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
     *  output="KI\UpontBundle\Entity\Dummy\ImdbSearchResponse",
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
     *  output="KI\UpontBundle\Entity\Dummy\ImdbInfosResponse",
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
     *  description="Recherche dans toute la base de données Ponthub",
     *  parameters={
     *   {
     *    "name"="name",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Le critère de recherche de base"
     *   },
     *   {
     *    "name"="type",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Le type de données à aller chercher"
     *   },
     *   {
     *    "name"="genre",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par genre"
     *   },
     *   {
     *    "name"="actor",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par acteur"
     *   },
     *   {
     *    "name"="artist",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par groupe/réalisateur"
     *   },
     *   {
     *    "name"="yearMin",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par année"
     *   },
     *   {
     *    "name"="yearMax",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par année"
     *   },
     *   {
     *    "name"="sizeMin",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par taille de fichier"
     *   },
     *   {
     *    "name"="sizeMax",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par taille de fichier"
     *   },
     *   {
     *    "name"="durationMin",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par durée de la vidéo"
     *   },
     *   {
     *    "name"="durationMax",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Recherche par durée de la vidéo"
     *   },
     *  },
     *  tags={
     *    "TODO"
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  output="KI\UpontBundle\Entity\Ponthub\PonthubFile",
     *  section="Ponthub"
     * )
     */
    public function searchAction(Request $request)
    {
        /*if (!$this->get('security.context')->isGranted('ROLE_USER'))
            throw new AccessDeniedException();
        $params = $request->request;
        $this->em = $this->get('doctrine')->getManager();
        $connection = $this->em->getConnection();
        $results = array();
        $type = $params->has('type') ? $params->get('type') : '';

        // On va chercher dans tous les repos les objets qui correspondent à la requête
        $repoA = $this->em->getRepository('KIUpontBundle:Ponthub\Album');
        $repoE = $this->em->getRepository('KIUpontBundle:Ponthub\Episode');
        $repoG = $this->em->getRepository('KIUpontBundle:Ponthub\Game');
        $repoMo = $this->em->getRepository('KIUpontBundle:Ponthub\Movie');
        $repoMu = $this->em->getRepository('KIUpontBundle:Ponthub\Music');
        $repoO = $this->em->getRepository('KIUpontBundle:Ponthub\Other');
        $repoS = $this->em->getRepository('KIUpontBundle:Ponthub\Serie');

        if ($type == '' || $type == 'movie') {
            $qb = $this->em->createQueryBuilder();
            $qb->select('m')
               ->from('Movie', 'm')
               ->where('u.id = ?1')
               ->orderBy('u.name', 'ASC');
        }*/
        return new Response();
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
     *  tags={
     *    "TODO"
     *  },
     *  section="Ponthub"
     * )
     */
    public function statisticsAction()
    {
        return new Response();
    }
}
