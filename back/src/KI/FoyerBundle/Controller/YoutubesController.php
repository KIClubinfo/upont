<?php

namespace KI\FoyerBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class YoutubesController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Youtube', 'Foyer');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les liens Youtube",
     *  output="KI\FoyerBundle\Entity\Youtube",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes")
     * @Method("GET")
     */
    public function getYoutubesAction()
    {
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un lien Youtube",
     *  output="KI\FoyerBundle\Entity\Youtube",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes/{slug}")
     * @Method("GET")
     */
    public function getYoutubeAction($slug)
    {
        return $this->getOne($slug);
    }

    /**
     * @ApiDoc(
     *  description="Crée un lien Youtube",
     *  input="KI\FoyerBundle\Form\YoutubeType",
     *  output="KI\FoyerBundle\Entity\Youtube",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes")
     * @Method("POST")
     */
    public function postYoutubeAction()
    {
        return $this->post($this->is('USER'));
    }

    /**
     * @ApiDoc(
     *  description="Supprime un lien Youtube",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/youtubes/{slug}")
     * @Method("DELETE")
     */
    public function deleteYoutubeAction($slug)
    {
        $author = $this->findBySlug($slug)->getUser();
        return $this->delete($slug, $this->user == $author);
    }
}
