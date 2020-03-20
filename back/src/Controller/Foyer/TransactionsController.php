<?php

namespace App\Controller\Foyer;

use App\Controller\ResourceController;
use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Beer;
use App\Helper\BeerHelper;
use App\Helper\TransactionHelper;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class TransactionsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(Transaction::class, null);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Liste toutes les transactions",
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
     * @Route("/transactions", methods={"GET"})
     */
    public function getTransactionsAction()
    {
        return $this->getAll($this->isFoyerMember());
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Liste les utilisateurs ayant bu dernièrement",
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
     * @Route("/userbeers", methods={"GET"})
     */
    public function getUserBeersAction(BeerHelper $beerHelper)
    {
        $this->trust($this->isFoyerMember());

        $users = $beerHelper->getUserOrderedList();
        return $this->json($users);
    }


    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Liste les transactions d'un utilisateur",
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
     * @Route("/users/{slug}/transactions", methods={"GET"})
     */
    public function getUserTransactionsAction(Request $request, $slug)
    {
        $userRepository = $this->getDoctrine()->getManager()->getRepository(User::class);
        $user = $userRepository->findOneByUsername($slug);

        $this->trust($this->isFoyerMember() || $this->user == $user);

        $request->query->set('user', $user->getId());

        return $this->getAll();
    }


    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Liste les transactions d'une bière",
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
     * @Route("/beers/{slug}/transactions", methods={"GET"})
     */
    public function getBeerTransactionsAction(Request $request, $slug)
    {
        $beerRepository = $this->getDoctrine()->getManager()->getRepository(Beer::class);
        $beer = $beerRepository->findOneBySlug($slug);

        $this->trust($this->isFoyerMember());

        $request->query->set('beer', $beer->getId());

        return $this->getAll();
    }


    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Crée une transaction - conso OU compte crédité OU livraison",
     *     @SWG\Parameter(
     *         name="user",
     *         in="formData",
     *         description="Le slug de l'utilisateur SI C'EST UNE CONSO OU UN CRÉDIT",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="beer",
     *         in="formData",
     *         description="Le slug de la bière SI C'EST UNE CONSO OU UNE LIVRAISON",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="credit",
     *         in="formData",
     *         description="Le montant à créditer SI C'EST UNE TRANSACTION DE CRÉDIT OU UNE LIVRAISON",
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
     * @Route("/transactions", methods={"POST"})
     */
    public function postTransactionAction(TransactionHelper $transactionHelper, Request $request)
    {
        $this->trust($this->isFoyerMember());

        $user = $request->request->get('user');
        $hasUser = !($user === null);

        $beer = $request->request->get('beer');
        $hasBeer = !($beer === null);

        $credit = $request->request->get('credit');
        $hasCredit = !($credit === null);

        $number = $request->request->get('number');

        if ($hasUser and $hasBeer and $hasCredit) {
            throw new BadRequestHttpException('Trop d\'info pour une transaction');
        }

        if ($hasUser and $hasBeer) {
            // conso
            $id = $transactionHelper->addBeerTransaction($user, $beer);
        } else if ($hasUser and $hasCredit) {
            // crédit
            $id = $transactionHelper->addCreditTransaction($user, $credit);
        } else if ($hasBeer and $hasCredit and $hasNumber) {
            // livraison
            $id = $transactionHelper->addDeliveryTransaction($beer, $credit, $number);
        } else {
            throw new BadRequestHttpException('Pas assez d\'info pour une transaction');
        }

        return $this->json($id, 201);
    }

    /**
     * @Operation(
     *     tags={"Foyer"},
     *     summary="Supprime une transaction",
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
     * @Route("/transactions/{id}", methods={"DELETE"})
     */
    public function deleteTransactionAction(TransactionHelper $transactionHelper, $id)
    {
        $this->trust($this->isFoyerMember());

        $transaction = $this->findBySlug($id);
        $user = $transaction->getUser();
        $beer = $transaction->getBeer();
        $amount = $transaction->getAmount();

        if (!($user === null)){
            $transactionHelper->updateBalance($user, -1 * $amount);
        }
        if (!($beer === null or $amount === 0)){
            $transactionHelper->updateStock($beer, - abs($amount) / $amount);
        }

        $this->delete($id, $this->isFoyerMember());

        return $this->json(null, 204);
    }
}
