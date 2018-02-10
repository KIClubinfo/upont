<?php

namespace App\Helper;

use App\Entity\Episode;
use App\Entity\Game;
use App\Entity\Movie;
use App\Entity\Other;
use App\Entity\Software;
use App\Entity\User;
use App\Repository\PonthubFileUserRepository;

class StatisticsHelper
{
    protected $ponthubFileUserRepository;

    public function __construct(PonthubFileUserRepository $ponthubFileUserRepository)
    {
        $this->ponthubFileUserRepository = $ponthubFileUserRepository;
    }

    /**
     * @param  User   $user
     * @return array
     */
    public function getUserStatistics(User $user)
    {
        $downloads = $this->ponthubFileUserRepository->findBy(['user' => $user], ['date' => 'ASC']);
        $totalFiles = count($downloads);

        if ($totalFiles == 0) {
            return ['repartition' => [], 'timeline' => [], 'totalSize' => 0, 'totalFiles' => 0, 'hipster' => 0];
        }

        // Récupération de la date javascript du premier download
        $date = 1000*($downloads[0]->getDate() - 10*3600);

        // Initialisation des tableaux à retourner
        $repartition = [
            ['Films', 0],
            ['Épisodes', 0],
            ['Jeux', 0],
            ['Logiciels', 0],
            ['Autres', 0]
        ];
        $timeline = [
            ['name' => 'Films',     'data' => [[$date, 0]]],
            ['name' => 'Épisodes',  'data' => [[$date, 0]]],
            ['name' => 'Jeux',      'data' => [[$date, 0]]],
            ['name' => 'Logiciels', 'data' => [[$date, 0]]],
            ['name' => 'Autres',    'data' => [[$date, 0]]]
        ];
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
            if ($file instanceof Game) {
                $repartition[2][1]++;
                $this->updateSeries($timeline, $date, 3);
            }
            if ($file instanceof Software) {
                $repartition[3][1]++;
                $this->updateSeries($timeline, $date, 4);
            }
            if ($file instanceof Other) {
                $repartition[4][1]++;
                $this->updateSeries($timeline, $date, 5);
            }
            $totalSize += $file->getSize();

            // Gain de points hipsteritude en fonction du nombre d'autres
            // personnes qui ont téléchargé le fichier
            $this->computeHipsteritude($hipster, $file->downloads() - 1);
        }

        // Dans le chart stacké, on met la date actuelle comme point de fin
        $this->updateSeries($timeline, time()*1000, -1);

        return [
            'repartition' => $repartition,
            'timeline'    => $timeline,
            'totalSize'   => $totalSize,
            'totalFiles'  => $totalFiles,
            'hipster'     => (int)(10*$hipster/$totalFiles)
        ];
    }

    /**
     * Met à jour une série de données pour le graphe de téléchargements cumulés
     * @param  array   &$series
     * @param  integer $date
     * @param  integer $id
     */
    private function updateSeries(&$series, $date, $id) {
        foreach ($series as $key => &$value) {
            if ($key != $id) {
                $value['data'][] = [$date, $value['data'][count($value['data']) - 1][1]];
            } else {
                $value['data'][] = [$date, $value['data'][count($value['data']) - 1][1] + 1];
            }
        }
    }

    /**
     * Met à jour la hipsteritude
     * @param  integer &$hipster La hipsteritude à mettre à jour
     * @param  integer $count    Le nombre d'autres personnes ayant téléchargé le fichier
     */
    private function computeHipsteritude(&$hipster, $count)
    {
        if ($count == 0) {
            $hipster += 20;
        } else if ($count < 2) {
            $hipster += 15;
        } else if ($count < 4) {
            $hipster += 10;
        } else if ($count < 9) {
            $hipster += 5;
        } else if ($count < 19) {
            $hipster += 3;
        } else {
            $hipster++;
        }
    }
}
