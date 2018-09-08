<?php

namespace App\Controller\Ponthub;

use App\Controller\ResourceController;
use App\Entity\PonthubFile;
use App\Entity\PonthubFileUser;

// Surcouche pour les fichiers de type PonthubFile
class PonthubFileController extends ResourceController
{
    /**
     * Enregistre un téléchargement Ponthub et redirige vers la Ressource
     * @param  PonthubFile $item Le fichier à télécharger
     * @return mixed             La ressource distante
     */
    protected function download(PonthubFile $item)
    {
        $this->trust(!$this->is('EXTERIEUR'));

        if (!$item->hasBeenDownloaded()) {
            $download = new PonthubFileUser();
            $download->setFile($item);
            $download->setUser($this->user);
            $download->setDate(time());
            $this->manager->persist($download);
            $this->manager->flush();
        }

        return $this->redirect($item->fileUrl());
    }
}
