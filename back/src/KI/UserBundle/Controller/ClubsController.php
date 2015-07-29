<?php

namespace KI\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\UserBundle\Form\ClubUserType;
use KI\UserBundle\Entity\ClubUser;

class ClubsController extends \KI\CoreBundle\Controller\SubresourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Club', 'User');
    }



    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les clubs",
     *  output="KI\UserBundle\Entity\Club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getClubsAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un club",
     *  output="KI\UserBundle\Entity\Club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée"
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getClubAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée un club",
     *  input="KI\UserBundle\Form\ClubType",
     *  output="KI\UserBundle\Entity\Club",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postClubAction() { return $this->post(); }

    /**
     * @ApiDoc(
     *  description="Modifie un club",
     *  input="KI\UserBundle\Form\ClubType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function patchClubAction($slug)
    {
        return $this->patch(
            $slug,
            $this->checkClubMembership($slug)
            && !$this->get('security.context')->isGranted('ROLE_EXTERIEUR')
            );
    }

    /**
     * @ApiDoc(
     *  description="Supprime un club",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function deleteClubAction($slug)
    {
        $repoLink = $this->em->getRepository('KIUserBundle:ClubUser');
        $club = $this->findBySlug($slug);
        $link = $repoLink->findBy(array('club' => $club));

        foreach ($link as $clubUser) {
            $this->em->remove($clubUser);
        }
        // TODO
        // S'arranger pour supprimer la bannière de façon automatique

        return $this->delete($slug);
    }

    /**
     * @ApiDoc(
     *  description="Liste les membres d'un club",
     *  output="KI\UserBundle\Entity\User",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getClubUsersAction($slug) { return $this->getAllSub($slug, 'User', false); }

    /**
     * @ApiDoc(
     *  description="Ajoute un membre à un club",
     *  requirements={
     *   {
     *    "name"="role",
     *    "dataType"="string",
     *    "description"="Le rôle du membre dans le club"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Post("/clubs/{slug}/users/{id}")
     */
    public function postClubUserAction($slug, $id)
    {
        if (!($this->get('security.context')->isGranted('ROLE_ADMIN')
            || $this->checkClubMembership($slug)))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $repo = $this->em->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($id);
        $club = $this->findBySlug($slug);

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->em->getRepository('KIUserBundle:ClubUser');
        $link = $repoLink->findBy(array('club' => $club, 'user' => $user));

        // On crée la relation si elle n'existe pas déjà
        if (count($link) == 0) {
            // Création de l'entité relation
            $link = new ClubUser();
            $link->setClub($club);
            $link->setUser($user);

            // Validation des données annexes
            $form = $this->createForm(new ClubUserType(), $link, array('method' => 'POST'));
            $form->handleRequest($this->getRequest());

            if ($form->isValid()) {
                $this->em->persist($link);
                $this->em->flush();

                return $this->jsonResponse(null, 204);
            } else {
                $this->em->detach($link);
                return $this->jsonResponse($form, 400);
            }
        } else
            throw new BadRequestHttpException('La relation entre Club et User existe déjà');
    }

    /**
     * @ApiDoc(
     *  description="Enlève un membre d'un club",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Delete("/clubs/{slug}/users/{id}")
     */
    public function deleteClubUserAction($slug, $id)
    {
        if (!($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->checkClubMembership($slug)))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $repo = $this->em->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($id);
        $club = $this->findBySlug($slug);

        // On récupère la relation
        $repoLink = $this->em->getRepository('KIUserBundle:ClubUser');
        $link = $repoLink->findBy(array('club' => $club, 'user' => $user));

        // Supprime la relation si elle existe
        if (count($link) == 1) {
            $this->em->remove($link[0]);
            $this->em->flush();
        } else
            throw new NotFoundHttpException('Relation entre Club et User non trouvée');
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Abonne un utilisateur à un club",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Post("/clubs/{slug}/follow")
     */
    public function followClubAction($slug)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $club = $this->findBySlug($slug);

        if (!$user->getClubsNotFollowed()->contains($club)) {
            throw new BadRequestHttpException('Vous êtes déjà abonné à ce club');
        } else {
            $user->removeClubNotFollowed($club);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Désabonne un utilisateur d'un club",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Post("/clubs/{slug}/unfollow")
     */
    public function unFollowClubAction($slug)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $club = $this->findBySlug($slug);

        if ($user->getClubsNotFollowed()->contains($club)) {
            throw new BadRequestHttpException('Vous n\'êtes pas abonné à ce club');
        } else {
            $user->addClubNotFollowed($club);
            $this->em->flush();

            return $this->restResponse(null, 204);
        }
    }

    /**
     * @ApiDoc(
     *  description="Retourne toutes les news publiées par un club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/clubs/{slug}/newsitems")
     */
    public function getNewsitemsClubAction($slug)
    {
        $repo = $this->em->getRepository('KIPublicationBundle:Newsitem');

        list($findBy, $sortBy, $limit, $offset, $page, $totalPages, $count) = $this->paginate($repo);
        $findBy['authorClub'] = $this->findBySlug($slug);
        $results = $repo->findBy($findBy, $sortBy, $limit, $offset);
        return $this->generatePages($results, $limit, $page, $totalPages, $count);
    }

    /**
     * @ApiDoc(
     *  description="Retourne tous les events publiés par un club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/clubs/{slug}/events")
     */
    public function getEventsClubAction($slug)
    {
        $repo = $this->em->getRepository('KIPublicationBundle:Event');

        list($findBy, $sortBy, $limit, $offset, $page, $totalPages, $count) = $this->paginate($repo);
        $findBy['authorClub'] = $this->findBySlug($slug);
        $results = $repo->findBy($findBy, $sortBy, $limit, $offset);
        return $this->generatePages($results, $limit, $page, $totalPages, $count);
    }
}