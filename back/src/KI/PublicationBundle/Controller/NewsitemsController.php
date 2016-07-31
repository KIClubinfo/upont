<?php

namespace KI\PublicationBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewsitemsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Newsitem', 'Publication');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les news",
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems")
     * @Method("GET")
     */
    public function getNewsitemsAction()
    {
        return $this->getAll($this->is('EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Retourne une news",
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems/{slug}")
     * @Method("GET")
     */
    public function getNewsitemAction($slug)
    {
        $newsitem = $this->getOne($slug, $this->is('EXTERIEUR'));

        return $this->json($newsitem);
    }

    /**
     * @ApiDoc(
     *  description="Crée une news",
     *  input="KI\PublicationBundle\Form\NewsitemType",
     *  output="KI\PublicationBundle\Entity\Newsitem",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems")
     * @Method("POST")
     */
    public function postNewsitemAction()
    {
        $data = $this->post($this->isClubMember());

        if ($data['code'] == 201) {
            $this->get('ki_publication.listener.newsitem')->postPersist($data['item']);
        }

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une news",
     *  input="KI\PublicationBundle\Form\NewsitemType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems/{slug}")
     * @Method("PATCH")
     */
    public function patchNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        $data = $this->patch($slug, $this->isClubMember($club));

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une news",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route("/newsitems/{slug}")
     * @Method("DELETE")
     */
    public function deleteNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;

        $this->delete($slug, $this->isClubMember($club));

        return $this->json(null, 204);
    }
}
