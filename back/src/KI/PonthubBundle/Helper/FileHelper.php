<?php

namespace KI\PonthubBundle\Helper;

use Doctrine\ORM\EntityManager;
use KI\PonthubBundle\Entity\Episode;
use KI\PonthubBundle\Entity\Game;
use KI\PonthubBundle\Entity\Movie;
use KI\PonthubBundle\Entity\Other;
use KI\PonthubBundle\Entity\Serie;
use KI\PonthubBundle\Entity\Software;
use KI\PonthubBundle\Entity\Genre;

/**
 * Class FileHelper
 * @package KI\PonthubBundle\Helper
 */
class FileHelper
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * FileHelper constructor.
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
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
        if ($size == 0) {
            return;
        }

        if (preg_match('#^/root/web/films/#', $path)) {
            $item = new Movie();
            $item = $this->basicInfos($item, $size, $path, $name);
            $this->manager->persist($item);
        }

        if (preg_match('#^/root/web/films_light/#', $path)) {
            $item = new Movie();
            $item = $this->basicInfos($item, $size, $path, $name);
            $this->manager->persist($item);
        }
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
        if (!preg_match('#^/root/web/series/#', $path)) {
            return;
        }

        // On détermine les différentes données
        $serie   = preg_replace('#/.*#', '', str_replace('/root/web/series/', '', $path));
        $episode = str_replace($ext, '', preg_replace('#.*/#', '', $path));

        // Si la série existe, on la récupère, sinon on la rajoute
        if (!isset($series[$serie])) {
            $serieItem = new Serie();
            $serieItem = $this->basicInfos($serieItem, null, '/root/web/series/'.$serie.'/', $serie);
            $this->manager->persist($serieItem);
            $series[$serie] = $serieItem;
        } else {
            $serieItem = $series[$serie];
        }

        if (!in_array('/root/web/series/'.$serie.'/', $pathsDone)) {
            $pathsDone[] = '/root/web/series/'.$serie.'/';
        }

        //On range l'épisode en commencant par déterminer le numéro de saison et d'épisode
        if (!preg_match('#^S([0-9]{2}) E([0-9]{2})#', $episode, $matches)) {
            return;
        }

        list(, $numberS, $numberE) = $matches;
        $item = new Episode();
        $item = $this->basicInfos($item, $size, $path, $name);
        $item->setStatus('OK');
        $item->setSeason($numberS);
        $item->setNumber($numberE);
        $item->setSerie($serieItem);

        // On actualise la date de modification de la série
        $serieItem->setAdded(time());
        $this->manager->persist($item);
        $pathsDone[] = $path;
    }

    /**
     * @param \KI\PonthubBundle\Entity\PonthubFile $item
     * @param integer $size
     * @param string $path
     * @param string $name
     * @return \KI\PonthubBundle\Entity\PonthubFile
     */
    private function basicInfos($item, $size, $path, $name)
    {
        $item->setSize($size);
        $item->setAdded(time());
        $item->setPath($path);
        $item->setStatus('NeedInfos');
        $item->setName($name);
        return $item;
    }
}
