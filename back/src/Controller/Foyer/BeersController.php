<?php

namespace App\Controller\Foyer;

use App\Controller\ResourceController;
use App\Entity\Beer;
use App\Entity\Transaction;
use App\Form\BeerType;
use App\Helper\BeerHelper;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BeersController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Beer::class, BeerType::class);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les bières, les premières sont les plus consommées",
     *  output="App\Entity\Beer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
          *  },
     *  section="Foyer"
     * )
     * @Route("/beers", methods={"GET"})
     */
    public function getBeersAction(BeerHelper $beerHelper)
    {
        $beers = $beerHelper->getBeerOrderedList();
        return $this->json($beers);
    }

    /**
     * @ApiDoc(
     *  description="Retourne une bière",
     *  output="App\Entity\Beer",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
          *  },
     *  section="Foyer"
     * )
     * @Route("/beers/{slug}", methods={"GET"})
     */
    public function getBeerAction($slug)
    {
        $beer = $this->getOne($slug);

        return $this->json($beer);
    }

    /**
     * @ApiDoc(
     *  description="Crée une bière",
     *  input="App\Form\BeerType",
     *  output="App\Entity\Beer",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
          *  },
     *  section="Foyer"
     * )
     * @Route("/beers", methods={"POST"})
     */
    public function postBeerAction()
    {
        $data = $this->post($this->isFoyerMember());

        return $this->formJson($data);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une bière",
     *  input="App\Form\BeerType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
          *  },
     *  section="Foyer"
     * )
     * @Route("/beers/{slug}", methods={"PATCH"})
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
          *  },
     *  section="Foyer"
     * )
     * @Route("/beers/{slug}", methods={"DELETE"})
     */
    public function deleteBeerAction($slug)
    {
        // On supprime toutes les consos associées
        $beer = $this->findBySlug($slug);
        $transactionRepository = $this->manager->getRepository(Transaction::class);
        $transactions = $transactionRepository->findBy(['beer' => $beer]);

        foreach ($transactions as $transaction) {
            $this->manager->remove($transaction);
        }

        $this->delete($slug, $this->isFoyerMember());

        return $this->json(null, 204);
    }
}
