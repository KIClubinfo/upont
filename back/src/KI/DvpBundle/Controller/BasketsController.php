<?php

namespace KI\DvpBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BasketsController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
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
     * @Route("/baskets")
     * @Method("GET")
     */
    public function getBasketsAction()
    {
        return $this->getAll($this->is('EXTERIEUR'));
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
     * @Route("/baskets/{slug}")
     * @Method("GET")
     */
    public function getBasketAction($slug)
    {
        $basket = $this->getOne($slug, $this->is('EXTERIEUR'));

        return $this->json($basket);
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
     * @Route("/baskets")
     * @Method("POST")
     */
    public function postBasketAction()
    {
        $data = $this->post($this->isClubMember('dvp'));

        return $this->formJson($data);
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
     * @Route("/baskets/{slug}")
     * @Method("PATCH")
     */
    public function patchBasketAction($slug)
    {
        $data = $this->patch($slug, $this->isClubMember('dvp'));

        return $this->formJson($data);
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
     * @Route("/baskets/{slug}")
     * @Method("DELETE")
     */
    public function deleteBasketAction($slug)
    {
        $basket = $this->getOne($slug);

        // On n'oublie pas de supprimer toutes les commandes associées
        $basketOrderRepository = $this->manager->getRepository('KIDvpBundle:BasketOrder');
        $basketOrder = $basketOrderRepository->findByBasket($basket);

        foreach ($basketOrder as $item) {
            $this->manager->remove($item);
        }

        $this->delete($slug, $this->isClubMember('dvp'));

        return $this->json(null, 204);
    }
}
