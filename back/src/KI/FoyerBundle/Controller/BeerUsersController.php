<?php

namespace KI\FoyerBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use KI\CoreBundle\Controller\ResourceController;
use KI\FoyerBundle\Entity\BeerUser;

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
     * @Route\Get("/beers/{slug}/users")
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
     * @Route\Post("/beers/{slug}/users/{beer}")
     */
    public function postBeerUserAction($slug, $beer)
    {
        if (!$this->checkClubMembership('foyer') && !$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        $repo = $this->getDoctrine()->getManager()->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($slug);
        $repo = $this->getDoctrine()->getManager()->getRepository('KIFoyerBundle:Beer');
        $beer = $repo->findOneBySlug($beer);

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
     * @Route\Delete("/beers/{slug}/users/{beer}/{id}")
     */
    public function deleteBeerUserAction($slug, $beer, $id)
    {
        return $this->delete($id, $this->checkClubMembership('foyer') && !$this->get('security.context')->isGranted('ROLE_ADMIN'));
    }
}
