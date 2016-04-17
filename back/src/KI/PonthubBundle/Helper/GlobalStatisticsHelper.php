<?php

namespace KI\PonthubBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\PonthubBundle\Entity\Episode;
use KI\PonthubBundle\Entity\Game;
use KI\PonthubBundle\Entity\Movie;
use KI\PonthubBundle\Entity\Other;
use KI\PonthubBundle\Entity\Software;

class GlobalStatisticsHelper
{
    protected $manager;
    protected $ponthubFileRepository;
    protected $ponthubFileUserRepository;

    public function __construct(EntityManager $manager, EntityRepository $ponthubFileRepository, EntityRepository $ponthubFileUserRepository)
    {
        $this->manager                   = $manager;
        $this->ponthubFileRepository     = $ponthubFileRepository;
        $this->ponthubFileUserRepository = $ponthubFileUserRepository;
    }

    /**
     * Hall of fame (histogramme) des plus gros downloaders
     * @return array
     */
    public function getGlobalDownloaders()
    {
        // Recherche des plus gros downloaders
        $dql = 'SELECT IDENTITY(e.user), SUM(f.size) AS compte FROM KI\PonthubBundle\Entity\PonthubFileUser e LEFT JOIN e.file f GROUP BY e.user ORDER BY compte DESC';
        $downloaderIds = $this->manager->createQuery($dql)->setMaxResults(10)->getResult();

        // On regarde les détails sur chaque utilisateur
        $downloaderCategories = array();
        $downloaderSeries = array(
            array('name' => 'Films',     'data' => array()),
            array('name' => 'Séries',    'data' => array()),
            array('name' => 'Jeux',      'data' => array()),
            array('name' => 'Logiciels', 'data' => array()),
            array('name' => 'Autres',    'data' => array())
        );

        foreach ($downloaderIds as $key => $value) {
            $downloads = $this->ponthubFileUserRepository->findBy(array('user' => $value[1]));

            $user = $downloads[0]->getUser();
            $downloaderCategories[] = $user->getFirstName().' '.$user->getLastName();
            for ($i = 0; $i < 6; $i++) {
                $downloaderSeries[$i]['data'][] = 0;
            }

            foreach ($downloads as $download) {
                $file = $download->getFile();
                $size = round($file->getSize()/(1000*1000*1000), 5);

                if ($file instanceof Movie) {
                    $downloaderSeries[0]['data'][$key] += $size;
                }
                if ($file instanceof Episode) {
                    $downloaderSeries[1]['data'][$key] += $size;
                }
                if ($file instanceof Game) {
                    $downloaderSeries[3]['data'][$key] += $size;
                }
                if ($file instanceof Software) {
                    $downloaderSeries[4]['data'][$key] += $size;
                }
                if ($file instanceof Other) {
                    $downloaderSeries[5]['data'][$key] += $size;
                }
            }
        }

        return array(
            'categories' => $downloaderCategories,
            'series'     => $downloaderSeries
        );
    }

    /**
     * Fichiers les plus téléchargés (avec drilldown)
     * @return array
     */
    public function getGlobalDownloads()
    {
        // Recherche des fichiers les plus téléchargés
        $downloadSerie = array(
            array('name' => 'Films', 'drilldown' => 1, 'y' => $this->getTotalDownloads('movie')),
            array('name' => 'Séries', 'drilldown' => 2, 'y' => $this->getTotalDownloads('episode')),
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
            array('name' => 'Jeux', 'id' => 4, 'data' => $this->getDownloads('game')),
            array('name' => 'Logiciels', 'id' => 5, 'data' => $this->getDownloads('software')),
            array('name' => 'Autres', 'id' => 6, 'data' => $this->getDownloads('other'))
        );

        return array(
            'serie' => $downloadSerie,
            'drilldown' => $downloadDrilldown
        );
    }

    /**
     * Renvoie le nombre total de fichiers par catégorie
     * @param  string $category
     * @return integer
     */
    private function getTotalDownloads($category) {
        $connection = $this->manager->getConnection();
        $statement = $connection->prepare('SELECT COUNT(*) AS compte FROM PonthubFileUser LEFT JOIN Likeable ON Likeable.id = PonthubFileUser.file_id WHERE Likeable.type = :category');
        $statement->bindValue('category', $category);
        $statement->execute();
        $results = $statement->fetchAll();

        return (int)$results[0]['compte'];
    }

    /**
     * Renvoie les fichiers les plus téléchargés pour une categorie
     * @param  string $category
     * @return array
     */
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

    /**
     * Répartition des fichiers dans tous le catalogue
     * @return array
     */
    public function getGlobalPonthub()
    {
        // Construction de la tree map résumant les fichiers dispos sur Ponthub
        return array(
            'Nombre de fichiers dispos' => array(
                'Films' => $this->getTotal('Movie'),
                'Séries' => $this->getTotal('Episode'),
                'Jeux' => $this->getTotal('Game'),
                'Logiciels' => $this->getTotal('Software'),
                'Autres' => $this->getTotal('Other')
            ),
            'Volume de fichiers (Go)' => array(
                'Films' => $this->getTotal('Movie', true),
                'Séries' => $this->getTotal('Episode', true),
                'Jeux' => $this->getTotal('Game', true),
                'Logiciels' => $this->getTotal('Software', true),
                'Autres' => $this->getTotal('Other', true)
            )
        );
    }

    /**
     * Retourne un total selon une catégorie de fichiers
     * @param  string  $category
     * @param  boolean $size     Si true, renvoie la taille totale, sinon le nombre total
     * @return integer
     */
    private function getTotal($category, $size = false) {
        if ($size) {
            $dql = 'SELECT SUM(e.size) FROM KI\PonthubBundle\Entity\\'.$category.' e';
            return $this->manager->createQuery($dql)->getSingleScalarResult()/(1000*1000*1000);
        } else {
            $dql = 'SELECT COUNT(e.id) FROM KI\PonthubBundle\Entity\\'.$category.' e';
            return $this->manager->createQuery($dql)->getSingleScalarResult();
        }
    }

    /**
     * Histo en barres horizontales de répartition par années
     * @return array
     */
    public function getGlobalYears()
    {
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

        return array(
            'categories' => $yearCategories,
            'series' => $yearSeries,
            'min' => -$maxPopMovie,
            'max' => $maxPopGame
        );
    }

    /**
     * Timeline avec camembert par promo et moyenne
     * @return array
     */
    public function getGlobalTimeline()
    {
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
        return $timeline;
    }
}
