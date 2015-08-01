<?php

namespace KI\FoyerBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BeersController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Beer', 'Foyer');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les bières",
     *  output="KI\FoyerBundle\Entity\Beer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function getBeersAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne une bière",
     *  output="KI\FoyerBundle\Entity\Beer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function getBeerAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée une bière",
     *  input="KI\FoyerBundle\Form\BeerType",
     *  output="KI\FoyerBundle\Entity\Beer",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function postBeerAction()
    {
        $return = $this->partialPost($this->checkClubMembership('foyer'));
        return $this->postView($return);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une bière",
     *  input="KI\FoyerBundle\Form\BeerType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function patchBeerAction($slug)
    {
        return $this->patch($slug, $this->checkClubMembership('foyer'));
    }

    /**
     * @ApiDoc(
     *  description="Supprime une bière",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     */
    public function deleteBeerAction($slug)
    {
        return $this->delete($slug, $this->checkClubMembership('foyer'));
    }
}
