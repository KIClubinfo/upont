<?php

namespace KI\FoyerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use KI\CoreBundle\Controller\ResourceController;
use KI\FoyerBundle\Entity\Beer;
use KI\FoyerBundle\Entity\BeerUser;
use KI\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BeerUsersController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('BeerUser', 'Foyer');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les consos",
     *  output="KI\FoyerBundle\Entity\BeerUser",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/beerusers")
     */
    public function getBeerUsersAction()
    {
        return $this->getAll();
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
     * @Route\Get("/userbeers")
     */
    public function getUserBeersAction()
    {
        $helper = $this->get('ki_foyer.helper.beer');
        $users = $helper->getUserOrderedList();
        return $this->restResponse($users);
    }

    /**
     * @ApiDoc(
     *  description="Liste les consos",
     *  output="KI\FoyerBundle\Entity\BeerUser",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Get("/users/{slug}/beers")
     */
    public function getBeersUserAction($slug)
    {
        $userRepository = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $userRepository->findOneByUsername($slug);

        $beerUsers = $this->repository->findBy(array('user' => $user));
        return $this->restResponse($beerUsers);
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
     * @Route\Post("/beers/{beer}/users/{slug}")
     */
    public function postBeerUserAction($slug, $beer)
    {
        $this->trust($this->isClubMember('foyer') || $this->is('ADMIN'));
        $helper = $this->get('ki_foyer.helper.beer');
        list($user, $beer) = $helper->updateBalance($slug, $beer);

        $beerUser = new BeerUser();
        $beerUser->setUser($user);
        $beerUser->setBeer($beer);
        $beerUser->setAmount(-1*$beer->getPrice());
        $this->manager->persist($beerUser);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Supprime une conso",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * Cette route est un peu spéciale : on fait un douvle check en demandant
     * username, beer et id. IL Y A DE L'ARGENT EN JEU !
     * @Route\Delete("/beers/{beer}/users/{slug}/{id}")
     */
    public function deleteBeerUserAction($slug, $beer, $id)
    {
        $this->trust($this->isClubMember('foyer') || $this->is('ADMIN'));
        $helper = $this->get('ki_foyer.helper.beer');
        list($user, $beer) = $helper->updateBalance($slug, $beer, true);

        return $this->delete($id, $this->isClubMember('foyer'));
    }



    /**
     * @ApiDoc(
     *  description="Modifie le solde d'un utilisateur",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   409="La requête ne peut être traitée à l’état actuel, problème de reconnaisance de nom",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Foyer"
     * )
     * @Route\Patch("/users/{slug}/balance")
     */
    public function patchBalanceAction($slug)
    {
        $this->trust($this->isClubMember('foyer') || $this->is('ADMIN'));

        $request = $this->getRequest()->request;
        if (!$request->has('balance')) {
            throw new BadRequestHttpException('Aucun crédit donné');
        }

        $userRepository = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $userRepository->findOneByUsername($slug);

        $balance = $user->getBalance();
        $balance = $balance === null ? 0 : $balance;
        $balance = $balance + $request->get('balance');

        $user->setBalance($balance);

        // On enregistre une entrée
        $beerUser = new BeerUser();
        $beerUser->setUser($user);
        $beerUser->setAmount($request->get('balance'));
        $this->manager->persist($beerUser);

        $this->manager->flush();

        return $this->jsonResponse(array('balance' => $user->getBalance()), 204);
    }
}
