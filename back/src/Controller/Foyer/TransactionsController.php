<?php

namespace App\Controller\Foyer;

use App\Controller\ResourceController;
use App\Entity\Transaction;
use App\Entity\User;
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
     *     summary="Crée une transaction - conso ou compte crédité (L'UN OU L'AUTRE, PAS LES DEUX EN MÊME TEMPS)",
     *     @SWG\Parameter(
     *         name="beer",
     *         in="formData",
     *         description="Le slug de la bière SI C'EST UNE CONSO",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="credit",
     *         in="formData",
     *         description="Le montant à créditer SI C'EST UNE TRANSACTION DE CRÉDIT",
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

        if (!$request->request->has('user')) {
            throw new BadRequestHttpException('User obligatoire');
        }
        if (!($request->request->has('beer') xor $request->request->has('credit'))) {
            throw new BadRequestHttpException('On rajoute une conso ou du crédit, pas les deux');
        }

        if ($request->request->has('beer')) {
            $id = $transactionHelper->addBeerTransaction($request->request->get('user'), $request->request->get('beer'));
        } else if ($request->request->has('credit')) {
            $id = $transactionHelper->addCreditTransaction($request->request->get('user'), $request->request->get('credit'));
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
        $transactionHelper->updateBalance($transaction->getUser(), -1 * $transaction->getAmount());

        $this->delete($id, $this->isFoyerMember());

        return $this->json(null, 204);
    }
}
