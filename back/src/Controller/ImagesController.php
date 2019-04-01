<?php

namespace App\Controller;

use App\Entity\Image;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImagesController extends BaseController
{
    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Permet d'uploader des images via le rédacteur",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/images", methods={"POST"})
     */
    public function postImageAction(Request $request)
    {
        if (!$request->files->has('file')) {
            throw new BadRequestHttpException('Aucun fichier fourni');
        }

        $image = new Image();
        $file = $request->files->get('file');
        $image->setExt($file->guessExtension());
        $image->setFile($file);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($image);
        $manager->flush();

        return $this->json([
            'filelink' => '../api/'.$image->getWebPath(),
        ], 201);
    }
}
