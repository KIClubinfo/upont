<?php

namespace KI\PonthubBundle\Controller;

use KI\CoreBundle\Controller\SubresourceController;
use KI\PonthubBundle\Entity\PonthubFileUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

// Surcouche pour les fichiers de type PonthubFile
class PonthubFileController extends SubresourceController
{
    /**
     * Enregistre un téléchargement Ponthub et redirige vers la Ressource
     * @param  PonthubFile $item Le fichier à télécharger
     * @return mixed             La ressource distante
     */
    protected function download($item)
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
