<?php

namespace KI\UpontBundle\Controller\Publications;

use KI\UpontBundle\Entity\Publications\Newsitem;
use KI\UpontBundle\Form\Publications\NewsitemType;
use KI\UpontBundle\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;

class NewsitemsController extends BaseController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Newsitem', 'Publications');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les news",
     *  output="KI\UpontBundle\Entity\Publications\Newsitem",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getNewsitemsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une news",
     *  output="KI\UpontBundle\Entity\Publications\Newsitem",
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
    public function getNewsitemAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une news",
     *  input="KI\UpontBundle\Form\Publications\NewsitemType",
     *  output="KI\UpontBundle\Entity\Publications\Newsitem",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function postNewsitemAction()
    {
        $return = $this->partialPost($this->checkClubMembership());

        // On modifie légèrement la ressource qui vient d'être créée
        $return['item']->setDate(time());
        $return['item']->setAuthorUser($this->container->get('security.context')->getToken()->getUser());
        $this->em->flush();

        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une news",
     *  input="KI\UpontBundle\Form\Publications\NewsitemType",
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
     */
    public function patchNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        return $this->patch($slug, $this->checkClubMembership($club));
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
     */
    public function deleteNewsitemAction($slug)
    {
        $club = $this->findBySlug($slug)->getAuthorClub();
        $club = $club ? $club->getSlug() : $club;
        return $this->delete($slug, $this->checkClubMembership($club));
    }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des gens qui likent",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Get("/newsitems/{slug}/like")
     */
    public function getLikeNewsitemAction($slug) { return $this->getLikes($slug); }

    /**
     * @ApiDoc(
     *  description="Retourne la liste des gens qui unlikent",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Get("/newsitems/{slug}/unlike")
     */
    public function getUnlikeNewsitemAction($slug) { return $this->getUnlikes($slug); }

    /**
     * @ApiDoc(
     *  description="Retourne les commentaires",
     *  statusCodes={
     *   200="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Get("/newsitems/{slug}/comments")
     */
    public function getCommentsNewsitemAction($slug) { return $this->getAllSub($slug, 'Comment', true); }

    /**
     * @ApiDoc(
     *  description="Like",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Post("/newsitems/{slug}/like")
     */
    public function likeNewsitemAction($slug) { return $this->like($slug); }

    /**
     * @ApiDoc(
     *  description="Unlike",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Post("/newsitems/{slug}/unlike")
     */
    public function unlikeNewsitemAction($slug) { return $this->unlike($slug); }

    /**
     * @ApiDoc(
     *  description="Enlève son like",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Delete("/newsitems/{slug}/like")
     */
    public function deleteLikeNewsitemAction($slug) { return $this->deleteLike($slug); }

    /**
     * @ApiDoc(
     *  description="Enlève son unlike",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Delete("/newsitems/{slug}/unlike")
     */
    public function deleteUnlikeNewsitemAction($slug) { return $this->deleteUnlike($slug); }
}
