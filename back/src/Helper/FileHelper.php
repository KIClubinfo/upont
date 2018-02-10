<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Episode;
use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Other;
use App\Entity\Serie;
use App\Entity\Software;

/**
 * Class FileHelper
 * @package App\Helper
 */
class FileHelper
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * FileHelper constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $path
     * @param string $name
     * @param integer $size
     */
    public function tryToStoreMovie($path, $name, $size)
    {
        if (!preg_match('#^/root/web/films/#', $path) || $size == 0) {
            return;
        }

        $item = new Movie();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

    /**
     * @param string $path
     * @param string $name
     * @param integer $size
     */
    public function tryToStoreGame($path, $name, $size)
    {
        if (!preg_match('#^/root/web/jeux/#', $path) || $size == 0) {
            return;
        }

        $item = new Game();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

    /**
     * @param string $path
     * @param string $name
     * @param integer $size
     */
    public function tryToStoreSoftware($path, $name, $size)
    {
        if (!preg_match('#^/root/web/logiciels/#', $path) || $size == 0) {
            return;
        }

        $item = new Software();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

    /**
     * @param string $path
     * @param string $name
     * @param integer $size
     */
    public function tryToStoreOther($path, $name, $size)
    {
        if (!preg_match('#^/root/web/autres/#', $path) || $size == 0) {
            return;
        }

        $item = new Other();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

    /**
     * @param Serie[] $series
     * @param string[] $pathsDone
     * @param string $ext
     * @param string $path
     * @param string $name
     * @param integer $size
     */
    public function tryToStoreSerie(&$series, &$pathsDone, $ext, $path, $name, $size)
    {
        if (!preg_match('#^/root/web/series/.+#', $path)) {
            return;
        }

        preg_match('#^(/root/web/series/(.+?)/)#', $path, $matches);

        list(, $seriePath, $serieName) = $matches;

        // Si la série existe, on la récupère, sinon on la rajoute
        if (!isset($series[$seriePath])) {
            $serieItem = new Serie();
            $serieItem = $this->basicInfos($serieItem, null, $seriePath, $serieName);
            $this->manager->persist($serieItem);
            $series[$seriePath] = $serieItem;
        } else {
            $serieItem = $series[$seriePath];
        }

        if (!in_array($seriePath, $pathsDone)) {
            $pathsDone[] = $seriePath;
        }

        //On range l'épisode en commencant par déterminer le numéro de saison et d'épisode
        if (!preg_match('#^S([0-9]{2}) E([0-9]{2})#', $name, $matches)) {
            return;
        }

        list(, $numSaison, $numEpisode) = $matches;
        $item = new Episode();
        $item = $this->basicInfos($item, $size, $path, $name);
        $item->setStatus('OK');
        $item->setSeason($numSaison);
        $item->setNumber($numEpisode);
        $item->setSerie($serieItem);

        // On actualise la date de modification de la série
        $serieItem->setAdded(time());
        $this->manager->persist($item);
        $pathsDone[] = $path;
    }

    /**
     * @param \App\Entity\PonthubFile $item
     * @param integer $size
     * @param string $path
     * @param string $name
     * @return \App\Entity\PonthubFile
     */
    private function basicInfos($item, $size, $path, $name)
    {
        $item->setSize($size);
        $item->setAdded(time());
        $item->setPath($path);
        $item->setStatus('OK');
        $item->setName($name);
        return $item;
    }
}
