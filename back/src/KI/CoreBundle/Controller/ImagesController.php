<?php

namespace KI\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use KI\CoreBundle\Entity\Image;

class ImagesController extends BaseController
{
    /**
     * @ApiDoc(
     *  description="Permet d'uploader des images via le rédacteur",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function postImageAction(Request $request)
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException('Aucun fichier fourni');
        }

        $image = new Image();
        $file = $this->getRequest()->files->get('file');
        $image->setExt($file->guessExtension());
        $image->setFile($file);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($image);
        $manager->flush();

        return $this->jsonResponse(array(
            'filelink' => '../api/'.$image->getWebPath(),
        ), 201);
    }
}
