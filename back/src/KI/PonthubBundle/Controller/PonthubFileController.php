<?php

namespace KI\PonthubBundle\Controller;

use KI\CoreBundle\Controller\SubresourceController;
use KI\PonthubBundle\Entity\PonthubFileUser;

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

        // Si l'utilisateur n'a pas déjà téléchargé ce fichier on le rajoute
        $repository = $this->manager->getRepository('KIPonthubBundle:PonthubFileUser');
        $downloads = $repository->findBy(array('file' => $item, 'user' => $this->user));

        if (count($downloads) == 0) {
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
