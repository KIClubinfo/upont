<?php

namespace KI\ClubinfoBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\CoreBundle\Controller\ResourceController;

class CommandeController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Commande', 'Clubinfo');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les commandes de centrales",
     *  output="KI\ClubinfoBundle\Entity\Commande",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     */
    public function getCommandesAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une commande",
     *  output="KI\ClubinfoBundle\Entity\Commande",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Get("/commandes/{slug}/{username}")
     */
    public function getCommandeAction($slug, $username) 
    {
        return $this->findOneBySlugAndUsername($slug, $username);
    }

    /**
     * @ApiDoc(
     *  description="Retourne les commandes associées à une centrale",
     *  output="KI\ClubinfoBundle\Entity\Commande",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Get("/commandes/{slug}")
     */
    public function getCommandeFromCentraleAction($slug)
    {
        return $this->findBySlug($slug);
    }
    /**
     * @ApiDoc(
     *  description="Retourne les commandes associées à un utilisateur",
     *  output="KI\ClubinfoBundle\Entity\Commande",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Get("/users/commandes/{username}")
     */
    public function getCommandeFromUserAction($username)
    {
        return $this->findByUsername($username);
    }

    /**
     * @ApiDoc(
     *  description="Crée une commande",
     *  input="KI\ClubinfoBundle\Form\CommandeType",
     *  output="KI\ClubinfoBundle\Entity\Commande",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Post("/commandes")
     */
    public function postCommandeAction()
    {
        return $this->post($this->get('security.context')->isGranted('ROLE_USER'));
    }

    /**
     * @ApiDoc(
     *  description="Modifie une commande",
     *  input="KI\ClubinfoBundle\Form\CommandeType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Patch("/commandes/{slug}/{username}")
     */
    public function patchCommandeAction($slug, $username)
    {
        $commande = $this->findByCentraleSlug($slug);

        return $this->patch($slug);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une commande",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Clubinfo"
     * )
     * @Route\Delete("/commandes/{slug}/{username}")
     */
    public function deleteCommandeAction($slug, $username)
    {
        $commande = $this->findBySlug($slug);
        $user = $this->get('security.context')->getToken()->getUser();
        return $this->delete($user, $user->getUsername() == $commande->getUser()->getUsername());
    }
}
