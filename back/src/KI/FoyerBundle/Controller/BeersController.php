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
     *  description="Liste les bières, les premières sont les plus consommées",
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
    public function getBeersAction()
    {
        // Route un peu particulière : on va ordonner les bières
        // par ordre décroissant de consommation
        // On commence par toutes les récupérer
        $beers = $this->repo->findAll();

        // On va établir les comptes sur les 500 dernières consos
        $repo = $this->em->getRepository('KIFoyerBundle:BeerUser');
        $beerUsers = $repo->findBy(array(), array('date' => 'DESC'), 500);

        $counts = array();
        foreach ($beerUsers as $beerUser) {
            $beerId = $beerUser->getBeer()->getId();

            if (!isset($counts[$beerId])) {
                $counts[$beerId] = 0;
            }

            $counts[$beerId] = $counts[$beerId]+1;
        }

        // On trie
        $return = $beerCounts = array();
        foreach ($beers as $beer) {
            $beerId = $beer->getId();

            $beerCounts[] = isset($counts[$beerId]) ? $counts[$beerId] : 0;
            $return[]     = $beer;
        }
        array_multisort($beerCounts, SORT_DESC, $return);

        return $this->restResponse($return);
    }

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
        // On supprime toutes les consos associées
        $beer = $this->findBySlug($slug);
        $repo = $this->em->getRepository('KIFoyerBundle:BeerUser');
        $beerUsers = $repo->findBy(array('beer' => $beer));

        foreach ($beerUsers as $beerUser) {
            $this->em->remove($beerUser);
        }
        return $this->delete($slug, $this->checkClubMembership('foyer'));
    }
}
