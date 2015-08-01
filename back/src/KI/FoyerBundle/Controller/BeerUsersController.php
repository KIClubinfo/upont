<?php

namespace KI\FoyerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use KI\CoreBundle\Controller\ResourceController;
use KI\FoyerBundle\Entity\Beer;
use KI\FoyerBundle\Entity\BeerUser;
use KI\UserBundle\Entity\User;

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
     *  resource=true,
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
        // Route un peu particulière : on va ordonner les utilisateurs
        // par ordre décroissant de date consommation
        // On commence par récupérer 500 dernières consos
        $repo = $this->em->getRepository('KIFoyerBundle:BeerUser');
        $beerUsers = $repo->findBy(array(), array('date' => 'DESC'), 500);

        $users = array();
        foreach ($beerUsers as $beerUser) {
            $user = $beerUser->getUser();

            if (!in_array($user, $users)) {
                $users[] = $user;
            }
            // On ne veut que 48 résultats
            if (count($users) >= 48) {
                break;
            }
        }

        return $this->restResponse($users);
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
     * @Route\Get("/users/{slug}/beers")
     */
    public function getBeersUserAction($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($slug);

        $beerUsers = $this->repo->findBy(array('user' => $user));
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
        list($user, $beer) = $this->update($slug, $beer);

        $beerUser = new BeerUser();
        $beerUser->setUser($user);
        $beerUser->setBeer($beer);
        $beerUser->setDate(time());
        $this->em->persist($beerUser);
        $this->em->flush();

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
        list($user, $beer) = $this->update($slug, $beer, true);

        return $this->delete($id, $this->checkClubMembership('foyer') && !$this->get('security.context')->isGranted('ROLE_ADMIN'));
    }

    // Met à jour le compte Foyer d'un utilisateur
    protected function update($slug, $beer, $add = false)
    {
        if (!$this->checkClubMembership('foyer') && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $repo = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($slug);
        if (!$user instanceOf User) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }

        $repo = $this->getDoctrine()->getManager()->getRepository('KIFoyerBundle:Beer');
        $beer = $repo->findOneBySlug($beer);
        if (!$beer instanceOf Beer) {
            throw new NotFoundHttpException('Bière non trouvée');
        }

        $balance = $user->getBalance();
        $balance = $balance === null ? 0 : $balance;
        $price = $beer->getPrice();
        $balance = $add ? $balance+$price : $balance-$price;
        $user->setBalance($balance);

        return array($user, $beer);
    }
}
