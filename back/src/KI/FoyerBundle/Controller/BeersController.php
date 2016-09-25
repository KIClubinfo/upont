<?php

namespace KI\FoyerBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BeersController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
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
     * @Route("/beers")
     * @Method("GET")
     */
    public function getBeersAction()
    {
        $beerHelper = $this->get('ki_foyer.helper.beer');
        $beers = $beerHelper->getBeerOrderedList();
        return $this->json($beers);
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
     * @Route("/beers/{slug}")
     * @Method("GET")
     */
    public function getBeerAction($slug)
    {
        $beer = $this->getOne($slug);

        return $this->json($beer);
    }

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
     * @Route("/beers")
     * @Method("POST")
     */
    public function postBeerAction()
    {
        $data = $this->post($this->isFoyerMember());

        return $this->formJson($data);
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
     * @Route("/beers/{slug}")
     * @Method("PATCH")
     */
    public function patchBeerAction($slug)
    {
        $data = $this->patch($slug, $this->isFoyerMember());

        return $this->formJson($data);
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
     * @Route("/beers/{slug}")
     * @Method("DELETE")
     */
    public function deleteBeerAction($slug)
    {
        // On supprime toutes les consos associées
        $beer = $this->findBySlug($slug);
        $transactionRepository = $this->manager->getRepository('KIFoyerBundle:Transaction');
        $transactions = $transactionRepository->findBy(['beer' => $beer]);

        foreach ($transactions as $transaction) {
            $this->manager->remove($transaction);
        }

        $this->delete($slug, $this->isFoyerMember());

        return $this->json(null, 204);
    }
}
