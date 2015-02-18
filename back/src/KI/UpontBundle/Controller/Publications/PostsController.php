<?php

namespace KI\UpontBundle\Controller\Publications;

use KI\UpontBundle\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PostsController extends BaseController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Post', 'Publications');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les publications (events, news, sondages...)",
     *  output="KI\UpontBundle\Entity\Publications\Post",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getPostsAction() { return $this->getAll(); }
}
