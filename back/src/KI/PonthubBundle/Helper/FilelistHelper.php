<?php

namespace KI\PonthubBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;

use KI\PonthubBundle\Repository\GenreRepository;
use KI\PonthubBundle\Repository\PonthubFileRepository;
use KI\PonthubBundle\Repository\SerieRepository;

class FilelistHelper
{
    protected $manager;
    protected $genreRepository;
    protected $serieRepository;
    protected $ponthubFileRepository;
    protected $validExtensions;
    protected $fileHelper;

    public function __construct(EntityManagerInterface $manager,
                                GenreRepository $genreRepository,
                                SerieRepository $serieRepository,
                                PonthubFileRepository $ponthubFileRepository,
                                array $validExtensions,
                                FileHelper $fileHelper)
    {
        $this->manager = $manager;
        $this->genreRepository = $genreRepository;
        $this->serieRepository = $serieRepository;
        $this->ponthubFileRepository = $ponthubFileRepository;
        $this->validExtensions = $validExtensions;
        $this->fileHelper = $fileHelper;
    }

    /**
     * Lit la liste des fichiers de Fleur et en déduit les opérations à faire
     * @param  string $list
     */
    public function parseFilelist($list)
    {
        // On va modifier les entités en fonction de la liste, on récupère les
        // chemins de toutes les entités Ponthub
        $pathsExisting = $this->ponthubFileRepository->createQueryBuilder('r')->select('r.path')->getQuery()->getScalarResult();
        $pathsExisting = array_map('current', $pathsExisting);
        $pathsDone = [];

        // On stocke les albums et les séries existantes
        extract($this->listExistingEntities());

        while (!feof($list)) {
            // On parcourt la liste ligne par ligne
            $result = $this->parseLine(fgets($list), $pathsDone, $pathsExisting);
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

        $this->markFilesNotFound($pathsExisting, $pathsDone);
    }

    /**
     * Sert pour le tri des fichiers enfants (épisode)
     * @return array
     */
    private function listExistingEntities()
    {
        $seriesOutput = $genresOutput = [];
        $series = $this->serieRepository->findAll();
        foreach ($series as $serie) {
            $seriesOutput[$serie->getPath()] = $serie;
        }

        // On liste aussi les genres
        $genres = $this->genreRepository->findAll();
        foreach ($genres as $genre) {
            $genresOutput[$genre->getName()] = $genre;
        }
        return ['genres' => $genresOutput, 'series' => $seriesOutput];
    }

    /**
     * Parse une ligne de la liste et renvoie les données correspondantes
     * @param  string $line
     * @param  array $pathsDone
     * @param  array $pathsExisting
     * @return array
     */
    private function parseLine($line, &$pathsDone, $pathsExisting)
    {
        // On enlève le caractère de fin de ligne
        $line = str_replace(["\r", "\n"], ['', ''], $line);

        // On récupère la taille du fichier et on l'enlève de la line
        // pour garder uniquement le chemin
        $match = [];
        preg_match('/^(([0-9]+)[\t ]*)/', $line, $match);

        // On vérifie que la line a bien la bonne syntaxe, càd
        // %size%          %path%
        if (!(isset($match[1]) && isset($match[2]))) {
            return [];
        }
        $size = $match[2] * 1000;
        $line = str_replace($match[1], '', $line);

        // On exclut tous les fichiers de type non valide
        $name = preg_replace(['#.*/#', '#\.[a-zA-Z0-9]+$#'], ['', ''], $line);
        $ext = strrchr($line, '.');
        if ($ext !== false) {
            $ext = strtolower(substr($ext, 1));
            if (!in_array($ext, $this->validExtensions)) {
                return [];
            }
        }

        // On ne crée une nouvelle entrée que si le fichier n'existe pas
        if (in_array($line, $pathsExisting)) {
            $pathsDone[] = $line;
            return [];
        }
        return ['line' => $line, 'name' => $name, 'size' => $size, 'ext' => $ext];
    }

    /**
     * Prend les fichiers non trouvés dans la liste et les marque comme tel
     * @param  array $pathsExisting
     * @param  array $pathsDone
     */
    private function markFilesNotFound($pathsExisting, $pathsDone)
    {
        // Maintenant on marque les fichiers non trouvés
        $notFound = array_diff($pathsExisting, $pathsDone);
        $items = $this->ponthubFileRepository->findByPath($notFound);

        foreach ($items as $item) {
            $item->setStatus('NotFound');
        }
        $this->manager->flush();
    }
}
