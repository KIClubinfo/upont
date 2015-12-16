<?php

namespace KI\DvpBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\CoreBundle\Controller\ResourceController;
use KI\DvpBundle\Entity\BasketOrder;

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
     * @Route\Get("/baskets-orders")
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
     * @Route\Get("/baskets-orders/{email}")
     */
    public function getBasketsOrderAction($email)
    {
        $basketOrder = $this->repository->findByEmail($email);

        if (count($basketOrder) == 0) {
            throw new NotFoundHttpException('Client non trouvé');
        }

        return $basketOrder;
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
     * @Route\Post("/baskets/{slug}/order")
     */
    public function postBasketOrderAction($slug)
    {
        $request = $this->getRequest()->request;

        $isAuthenticated = $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');

        // Si l'utilisateur n'est pas dans uPont il doit avoir rempli les infos
        if (!$isAuthenticated) {
            if (!($request->has('firstName')
                && $request->has('lastName')
                && $request->has('email')
                && $request->has('phone')
            )) {
                throw new BadRequestHttpException('Formulaire incomplet');
            }
        } else if ($this->user->getPhone() === null && !$request->has('phone')) {
            throw new BadRequestHttpException('Formulaire incomplet');
        }

        // On vérifie que la commande n'a pas déjà été faite
        $basketRepository = $this->manager->getRepository('KIDvpBundle:Basket');
        $basket = $basketRepository->findOneBySlug($slug);

        $basketOrder = $this->repository->findOneBy(array(
            'basket' => $basket,
            'email' => $isAuthenticated ? $this->user->getEmail() : $request->get('email'),
            'dateRetrieve' => $request->get('dateRetrieve'),
        ));

        if ($basketOrder !== null) {
            throw new BadRequestHttpException('Tu as déjà commandé !');
        }

        $basketOrder = new BasketOrder();
        $basketOrder->setBasket($basket);

        if (!$isAuthenticated) {
            // Si l'user n'est pas sur uPont il a tout rempli dans le form
            $basketOrder->setFirstName($request->get('firstName'));
            $basketOrder->setLastName($request->get('lastName'));
            $basketOrder->setEmail($request->get('email'));
            $basketOrder->setPhone($request->get('phone'));
        } else {
            $user = $this->user;
            // Sinon on récupère les infos de son compte
            $basketOrder->setUser($user);
            $basketOrder->setFirstName($user->getFirstName());
            $basketOrder->setLastName($user->getLastName());
            $basketOrder->setEmail($user->getEmail());
            if ($user->getPhone() === null) {
                $user->setPhone($request->get('phone'));
            }
            $basketOrder->setPhone($user->getPhone());
        }

        $basketOrder->setDateOrder(time());
        $basketOrder->setDateRetrieve($request->get('dateRetrieve'));
        $basketOrder->setPaid(false);

        $this->manager->persist($basketOrder);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Modifie une commande",
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
     * @Route\Patch("/baskets/{slug}/order/{email}")
     */
    public function patchBasketOrderAction($slug, $email)
    {
        $this->trust($this->is('MODO') || $this->isClubMember('dvp'));

        $request = $this->getRequest()->request;
        if (!$request->has('dateRetrieve')) {
            throw new BadRequestHttpException('Paramètre manquant');
        }

        $userRepository = $this->manager->getRepository('KIUserBundle:User');

        // On identifie les utilisateurs par leur mail
        $basketRepository = $this->manager->getRepository('KIDvpBundle:Basket');

        $basketOrder = $this->repository->findOneBy(array(
            'basket' => $basketRepository->findOneBySlug($slug),
            'email' => $email,
            'dateRetrieve' => $request->get('dateRetrieve'),
        ));

        if ($basketOrder === null) {
            throw new BadRequestHttpException('Commande non trouvée');
        }

        // On patche manuellement
        if ($request->has('paid')) {
            $basketOrder->setPaid($request->get('paid'));
        }

        $this->manager->persist($basketOrder);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
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
     *  section="DévelopPonts"
     * )
     * @Route\Delete("/baskets/{slug}/order/{email}/{dateRetrieve}")
     */
    public function deleteBasketOrderAction($slug, $email, $dateRetrieve)
    {
        // On identifie les utilisateurs par leur mail
        $userRepository = $this->manager->getRepository('KIUserBundle:User');
        $user = $userRepository->findOneByEmail($email);
        $basketRepository = $this->manager->getRepository('KIDvpBundle:Basket');

        $basketOrder = $this->repository->findOneBy(array(
            'basket' => $basketRepository->findOneBySlug($slug),
            'email' => $email,
            'dateRetrieve' => $dateRetrieve
        ));

        if ($basketOrder === null) {
            throw new NotFoundHttpException('Commande non trouvée');
        }

        $this->manager->remove($basketOrder, $this->isClubMember('dvp'));
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
    }
}