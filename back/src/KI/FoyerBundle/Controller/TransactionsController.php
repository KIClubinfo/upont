<?php

namespace KI\FoyerBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\FoyerBundle\Helper\BeerHelper;
use KI\FoyerBundle\Helper\TransactionHelper;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TransactionsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Transaction', 'Foyer');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste toutes les transactions",
     *  output="KI\FoyerBundle\Entity\Transaction",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/transactions")
     * @Method("GET")
     */
    public function getTransactionsAction()
    {
        return $this->getAll($this->isFoyerMember());
    }

    /**
     * @ApiDoc(
     *  description="Liste les utilisateurs ayant bu dernièrement",
     *  output="KI\UserBundle\Entity\User",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/userbeers")
     * @Method("GET")
     */
    public function getUserBeersAction(BeerHelper $beerHelper)
    {
        $this->trust($this->isFoyerMember());

        $users = $beerHelper->getUserOrderedList();
        return $this->json($users);
    }

    /**
     * @ApiDoc(
     *  description="Liste les transactions d'un utilisateur",
     *  output="KI\FoyerBundle\Entity\Transaction",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/users/{slug}/transactions")
     * @Method("GET")
     */
    public function getUserTransactionsAction(Request $request, $slug)
    {
        $userRepository = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $userRepository->findOneByUsername($slug);

        $this->trust($this->isFoyerMember() || $this->user == $user);

        $request->query->set('user', $user->getId());

        return $this->getAll();
    }


    /**
     * @ApiDoc(
     *  description="Crée une transaction - conso ou compte crédité (L'UN OU L'AUTRE, PAS LES DEUX EN MÊME TEMPS)",
     *  requirements={
     *   {
     *    "name"="user",
     *    "dataType"="string",
     *    "description"="Le slug de l'utilisateur"
     *   }
     *  },
     *  parameters={
     *   {
     *    "name"="beer",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Le slug de la bière SI C'EST UNE CONSO"
     *   },
     *   {
     *    "name"="credit",
     *    "dataType"="string",
     *    "required"=false,
     *    "description"="Le montant à créditer SI C'EST UNE TRANSACTION DE CRÉDIT"
     *   }
     *  },
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/transactions")
     * @Method("POST")
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
     * @ApiDoc(
     *  description="Supprime une transaction",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route("/transactions/{id}")
     * @Method("DELETE")
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
