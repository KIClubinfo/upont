<?php

namespace KI\PublicationBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Route;
use KI\CoreBundle\Controller\ResourceController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsitemFilesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('NewsitemFile', 'Publication');
    }

    /**
     * @ApiDoc(
     *  description="Retourne un fichier lié à une news",
     *  output="KI\PublicationBundle\Entity\NewsitemFile",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getNewsitemfileAction($id)
    {
        $newsitemFile = $this->findBySlug($id);

        if (!file_exists($newsitemFile->getAbsolutePath())) {
            throw new NotFoundHttpException('Fichier non trouvé');
        }

        // On lit le fichier
        $response = new Response();
        $filepath = $newsitemFile->getAbsolutePath();

        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($filepath));
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$newsitemFile->getName().'";');
        $response->headers->set('Content-length', filesize($filepath));

        $response->sendHeaders();
        return $response->setContent(readfile($filepath));
    }
}
