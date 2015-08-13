<?php

namespace KI\DvpBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use KI\DvpBundle\Entity\BasketOrder;

class BasketsController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Basket', 'Dvp');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les paniers",
     *  output="KI\DvpBundle\Entity\Basket",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     */
    public function getBasketsAction()
    {
        return $this->getAll($this->get('security.context')->isGranted('ROLE_EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Retourne un panier",
     *  output="KI\DvpBundle\Entity\Basket",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     */
    public function getBasketAction($slug)
    {
        return $this->getOne($slug, $this->get('security.context')->isGranted('ROLE_EXTERIEUR'));
    }

    /**
     * @ApiDoc(
     *  description="Crée un panier",
     *  input="KI\DvpBundle\Form\BasketType",
     *  output="KI\DvpBundle\Entity\Basket",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     */
    public function postBasketAction()
    {
        return $this->post($this->isClubMember('dvp'));
    }

    /**
     * @ApiDoc(
     *  description="Modifie un panier",
     *  input="KI\DvpBundle\Form\BasketType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     */
    public function patchBasketAction($slug)
    {
        return $this->patch($slug, $this->isClubMember('dvp'));
    }

    /**
     * @ApiDoc(
     *  description="Supprime un panier",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="DévelopPonts"
     * )
     */
    public function deleteBasketAction($slug)
    {
        $basket = $this->findBySlug($slug);

        // On n'oublie pas de supprimer toutes les commandes associées
        $repository = $this->manager->getRepository('KIDvpBundle:BasketOrder');
        $basketOrder = $repository->findByBasket($basket);

        foreach ($basketOrder as $item) {
            $this->manager->remove($item);
        }

        return $this->delete($slug, $this->isClubMember('dvp'));
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
        $basket = $this->findBySlug($slug);
        $user = $this->get('security.context')->getToken()->getUser();

        // On vérifie que la commande n'a pas déjà été faite
        $repository = $this->manager->getRepository('KIDvpBundle:BasketOrder');
        $basketOrder = $repository->findBy(array('basket' => $basket, 'user' => $user));

        if (count($basketOrder) != 0)
            return;

        $basketOrder = new BasketOrder();
        $basketOrder->setBasket($basket);
        $basketOrder->setUser($user);
        $basketOrder->setDateOrder(time());

        // Si l'utilisateur n'est pas dans uPont on remplit les infos
        $request = $this->getRequest()->request;
        if (($user === null && !($request->has('firstName')
                                    && $request->has('lastName')
                                    && $request->has('email')
                                    && $request->has('phone')))
            || ($user->getPhone() === null && !$request->has('phone'))
           )
            throw new BadRequestHttpException('Formulaire incomplet');

        if ($user === null) {
            $basketOrder->setFirstName($request->get('firstName'));
            $basketOrder->setLastName($request->get('lastName'));
            $basketOrder->setEmail($request->get('email'));
        }

        if ($user === null || $user->getPhone() === null) {
            $basketOrder->setPhone($request->get('phone'));
            $user->setPhone($request->get('phone'));
        } else {
            $basketOrder->setPhone($user->getPhone());
            $basketOrder->setFirstName($user->getFirstName());
            $basketOrder->setLastName($user->getLastName());
            $basketOrder->setEmail($user->getEmail());
        }

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
     * @Route\Patch("/baskets/{slug}/order/{username}")
     */
    public function patchBasketOrderAction($slug, $username)
    {
        $basket = $this->findBySlug($slug);
        $repository = $this->manager->getRepository('KIUserBundle:User');
        $user = $repository->findOneByUsername($username);

        // On vérifie que la commande existe
        $this->switchClass('BasketOrder');
        $basketOrder = $this->repository->findBy(array('basket' => $basket, 'user' => $user));

        if (count($basketOrder) != 1)
            throw new BadRequestHttpException('Commande non trouvée');

        return $this->patch($basketOrder[0]->getId(), $this->isClubMember('dvp'));
    }
}
