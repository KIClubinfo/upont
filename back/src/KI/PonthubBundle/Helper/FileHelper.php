<?php

namespace KI\PonthubBundle\Helper;

use Doctrine\ORM\EntityManager;
use KI\PonthubBundle\Entity\Album;
use KI\PonthubBundle\Entity\Episode;
use KI\PonthubBundle\Entity\Game;
use KI\PonthubBundle\Entity\Movie;
use KI\PonthubBundle\Entity\Music;
use KI\PonthubBundle\Entity\Other;
use KI\PonthubBundle\Entity\Serie;
use KI\PonthubBundle\Entity\Software;
use KI\PonthubBundle\Entity\Genre;

class FileHelper
{
    protected $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function tryToStoreMovie($path, $name, $size)
    {
        if ($size == 0) {
            return;
        }

        if (preg_match('#^/root/web/films/#', $path)) {
            $item = new Movie();
            $item = $this->basicInfos($item, $size, $path, $name);
            $item->setVo(true);
            $item->setVost(true);
            $item->setHd(true);
            $this->manager->persist($item);
        }

        if (preg_match('#^/root/web/films_light/#', $path)) {
            $item = new Movie();
            $item = $this->basicInfos($item, $size, $path, $name);
            $item->setHd(false);
            $this->manager->persist($item);
        }
    }

    public function tryToStoreGame($path, $name, $size)
    {
        if (!preg_match('#^/root/web/jeux/#', $path) || $size == 0) {
            return;
        }

        $item = new Game();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

    public function tryToStoreSoftware($path, $name, $size)
    {
        if (!preg_match('#^/root/web/logiciels/#', $path) || $size == 0) {
            return;
        }

        $item = new Software();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

    public function tryToStoreOther($path, $name, $size)
    {
        if (!preg_match('#^/root/web/autres/#', $path) || $size == 0) {
            return;
        }

        $item = new Other();
        $item = $this->basicInfos($item, $size, $path, $name);
        $this->manager->persist($item);
    }

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
            $serieItem->setVo(true);
            $serieItem->setHd(false);
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

    public function tryToStoreAlbum(&$genres, &$albums, &$pathsDone, $path, $name, $size)
    {
        if (!preg_match('#^/root/web/musiques/#', $path)) {
            return;
        }

        // On détermine les différentes données
        $genre  = preg_replace('#/.*#', '', str_replace('/root/web/musiques/', '', $path));
        $artist = preg_replace('#/.*#', '', str_replace('/root/web/musiques/'.$genre.'/', '', $path));
        $album  = preg_replace('#/.*#', '', str_replace('/root/web/musiques/'.$genre.'/'.$artist.'/', '', $path));

        // Si le genre existe, on le récupère, sinon on le rajoute
        if (!isset($genres[$genre])) {
            $genreItem = new Genre();
            $genreItem->setName($genre);
            $this->manager->persist($genreItem);
            $genres[$genre] = $genreItem;
        } else {
            $genreItem = $genres[$genre];
        }

        // Si l'album existe, on le récupère, sinon on le rajoute
        if (!isset($albums[$album])) {
            $albumItem = new Album();
            $albumItem = $this->basicInfos($albumItem, null, '/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/', $album);
            $albumItem->setArtist($artist);
            $albumItem->setStatus('NeedInfos');
            $this->manager->persist($albumItem);
            $albums[$album] = $albumItem;
            $pathsDone[] = '/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/';
        } else {
            $albumItem = $albums[$album];
        }

        if (!in_array('/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/', $pathsDone)) {
            $pathsDone[] = '/root/web/musiques/'.$genre.'/'.$artist.'/'.$album.'/';
        }

        // Maintenant on range la musique
        $item = new Music();
        $item = $this->basicInfos($item, $size, $path, $name);
        $item->setStatus('OK');
        $item->addGenre($genreItem);
        $item->setAlbum($albumItem);
        $this->manager->persist($item);
        $pathsDone[] = $path;
    }

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
