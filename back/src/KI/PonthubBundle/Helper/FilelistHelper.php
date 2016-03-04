<?php

namespace KI\PonthubBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class FilelistHelper
{
    protected $manager;
    protected $genreRepository;
    protected $serieRepository;
    protected $ponthubFileRepository;
    protected $validExtensions;
    protected $fileHelper;

    public function __construct(EntityManager $manager,
                                EntityRepository $genreRepository,
                                EntityRepository $serieRepository,
                                EntityRepository $ponthubFileRepository,
                                array $validExtensions,
                                FileHelper $fileHelper)
    {
        $this->manager               = $manager;
        $this->genreRepository       = $genreRepository;
        $this->serieRepository       = $serieRepository;
        $this->ponthubFileRepository = $ponthubFileRepository;
        $this->validExtensions       = $validExtensions;
        $this->fileHelper            = $fileHelper;
    }

    /**
     * Lit la liste des fichiers de Fleur et en déduit les opérations à faire
     * @param  string $list
     */
    public function parseFilelist($list)
    {
        // On va modifier les entités en fonction de la liste, on récupère les
        // chemins de toutes les entités Ponthub
        $paths = $this->ponthubFileRepository->createQueryBuilder('r')->select('r.path')->getQuery()->getScalarResult();
        $paths = array_map('current', $paths);
        $pathsDone = array();

        // On stocke les albums et les séries existantes
        extract($this->listExistingEntities());

        while (!feof($list)) {
            // On parcourt la liste ligne par ligne
            $result = $this->parseLine(fgets($list), $pathsDone, $paths);
            if (empty($result)) {
                continue;
            }
            extract($result);

            // On détermine le dossier dans lequel est rangé le fichier et on range selon le type.
            $this->fileHelper->tryToStoreMovie($line, $name, $size);
            $this->fileHelper->tryToStoreGame($line, $name, $size);
            $this->fileHelper->tryToStoreSoftware($line, $name, $size);
            $this->fileHelper->tryToStoreOther($line, $name, $size);

            $this->fileHelper->tryToStoreSerie($series, $pathsDone, $ext, $line, $name, $size);
        }
        $this->manager->flush();

        $this->markFilesNotFound($paths, $pathsDone);
    }

    /**
     * Sert pour le tri des fichiers enfants (épisode)
     * @return array
     */
    private function listExistingEntities()
    {
        $series = $genres = array();
        $result = $this->serieRepository->findAll();
        foreach ($result as $serie) {
            $series[$serie->getName()] = $serie;
        }
        // On liste aussi les genres pour les musiques
        $result = $this->genreRepository->findAll();
        foreach ($result as $genre) {
            $genres[$genre->getName()] = $genre;
        }
        return array('genres' => $genres, 'series' => $series);
    }

    /**
     * Parse une ligne de la liste et renvoie les données correspondantes
     * @param  string $line
     * @param  array  $pathsDone
     * @param  array  $paths
     * @return array
     */
    private function parseLine($line, &$pathsDone, $paths)
    {
        // On enlève le caractère de fin de ligne
        $line = str_replace(array("\r", "\n"), array('', ''), $line);

        // On récupère la taille du fichier et on l'enlève de la line
        // pour garder uniquement le chemin
        $match = array();
        preg_match('/^(([0-9]+)[\t ]*)/', $line, $match);

        // On vérifie que la line a bien la bonne syntaxe, càd
        // %size%          %path%
        if (!(isset($match[1]) && isset($match[2]))) {
            return array();
        }
        $size = $match[2]*1000;
        $line = str_replace($match[1], '', $line);

        // On exclut tous les fichiers de type non valide
        $name = preg_replace(array('#.*/#', '#\.[a-zA-Z0-9]+$#'), array('', ''), $line);
        $ext = strtolower(substr(strrchr($line, '.'), 1));
        if (!in_array($ext, $this->validExtensions)) {
            return array();
        }

        // On ne crée une nouvelle entrée que si le fichier n'existe pas
        if (in_array($line, $paths)) {
            $pathsDone[] = $line;
            return array();
        }
        return array('line' => $line, 'name' => $name, 'size' => $size, 'ext' => $ext);
    }

    /**
     * Prend les fichiers non trouvés dans la liste et les marque comme tel
     * @param  array $paths
     * @param  array $pathsDone
     */
    private function markFilesNotFound($paths, $pathsDone) {
        // Maintenant on marque les fichiers non trouvés
        $notFound = array_diff($paths, $pathsDone);
        $items = $this->ponthubFileRepository->findByPath($notFound);

        foreach ($items as $item) {
            if (get_class($item) != 'KI\PonthubBundle\Entity\Serie') {
                $item->setStatus('NotFound');
            }
        }
        $this->manager->flush();
    }
}
