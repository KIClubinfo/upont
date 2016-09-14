<?php

namespace KI\DvpBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\DvpBundle\Entity\BasketOrder;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BasketOrdersController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('BasketOrder', 'Dvp');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste toutes les commandes",
     *  output="KI\DvpBundle\Entity\Basket",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     * @Route("/baskets-orders")
     * @Method("GET")
     */
    public function getBasketsOrdersAction()
    {
        return $this->getAll($this->isClubMember('dvp'));
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les commandes d'un utilisateur",
     *  output="KI\DvpBundle\Entity\Basket",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     * @Route("/baskets-orders/{email}")
     * @Method("GET")
     */
    public function getBasketsOrderAction($email)
    {
        $basketOrder = $this->repository->findByEmail($email);

        return $this->json($basketOrder);
    }

    /**
     * @ApiDoc(
     *  description="Crée une commande",
     *  requirements={
     *   {
     *    "name"="email",
     *    "dataType"="string",
     *    "description"="Adresse mail du client"
     *   },
     *   {
     *    "name"="phone",
     *    "dataType"="string",
     *    "description"="Numéro de téléphone du client"
     *   }
     *  },
     *  input="KI\DvpBundle\Form\BasketOrderType",
     *  output="KI\DvpBundle\Entity\BasketOrder",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     * @Route("/baskets-orders")
     * @Method("POST")
     */
    public function postBasketOrderAction(Request $request)
    {
        $orders = $request->request->get('orders');
        $isAuthenticated = $this->isGranted('IS_AUTHENTICATED_REMEMBERED');

        if(!$isAuthenticated) {
            $firstName = $request->request->get('firstName');
            $lastName = $request->request->get('lastName');
            $email = $request->request->get('email');
            $phone = $request->request->get('phone');
        } else {
            $user = $this->user;
            $firstName = $user->getFirstName();
            $lastName = $user->getLastName();
            $email = $user->getEmail();
            $phone = $user->getPhone();
        }

        // Si l'utilisateur n'est pas dans uPont il doit avoir rempli les infos
        if (!($firstName && $lastName && $email && $phone)) {
            throw new BadRequestHttpException('Formulaire incomplet');
        }

        foreach ($orders as $order) {
            $basketSlug = $order['basket'];
            $dateRetrieve = $order['dateRetrieve'];
            $ordered = $order['ordered'];

            $basketRepository = $this->manager->getRepository('KIDvpBundle:Basket');
            $basket = $basketRepository->findOneBySlug($basketSlug);

            $basketDateRepository = $this->manager->getRepository('KIDvpBundle:BasketDate');
            $basketDate = $basketDateRepository->findOneById($dateRetrieve);

            $basketOrder = $this->repository->findOneBy([
                'basket' => $basket,
                'email' => $email,
                'dateRetrieve' => $basketDate,
            ]);

            if($basketDate->isLocked())
                continue;

            if($ordered && $basketOrder === null) {
                $basketOrder = new BasketOrder();
                $basketOrder->setBasket($basket);

                $basketOrder->setFirstName($firstName);
                $basketOrder->setLastName($lastName);
                $basketOrder->setEmail($email);
                $basketOrder->setPhone($phone);

                $basketOrder->setDateOrder(time());
                $basketOrder->setDateRetrieve($basketDate);
                $basketOrder->setPaid(false);

                $this->manager->persist($basketOrder);
            }

            if(!$ordered && $basketOrder !== null){
                $this->manager->remove($basketOrder);
            }
        }
        $this->manager->flush();

        return $this->json(null, 204);
    }
}
