<?php

namespace App\Controller\Foyer;

use App\Controller\ResourceController;
use App\Entity\Beer;
use App\Entity\Transaction;
use App\Form\BeerType;
use App\Helper\BeerHelper;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;

class BeersController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Beer::class, BeerType::class);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Liste les bières, les premières sont les plus consommées",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/beers", methods={"GET"})
     */
    public function getBeersAction(BeerHelper $beerHelper)
    {
        $beers = $beerHelper->getBeerOrderedList();
        return $this->json($beers);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Retourne une bière",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/beers/{slug}", methods={"GET"})
     */
    public function getBeerAction($slug)
    {
        $beer = $this->getOne($slug);

        return $this->json($beer);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Crée une bière",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="alcohol",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="volume",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number"
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/beers", methods={"POST"})
     */
    public function postBeerAction()
    {
        $data = $this->post($this->isFoyerMember());

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Modifie une bière",
     *     @SWG\Parameter(
     *         name="name",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number",
     *     ),
     *     @SWG\Parameter(
     *         name="alcohol",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number",
     *     ),
     *     @SWG\Parameter(
     *         name="volume",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="number",
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/beers/{slug}", methods={"PATCH"})
     */
    public function patchBeerAction($slug)
    {
        $data = $this->patch($slug, $this->isFoyerMember());

        return $this->formJson($data);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Supprime une bière",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
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

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Masque ou affiche une bière",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/beers/{slug}/active", methods={"PATCH"})
     */
    public function activeBeerAction($slug)
    {
        $beer = $this->findBySlug($slug);
        $beer->setActive(!$beer->getActive());
        $this->manager->flush();
        return $this->json(null, 204);
    }
}
