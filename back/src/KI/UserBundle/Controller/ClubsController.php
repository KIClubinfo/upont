<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\SubresourceController;
use KI\UserBundle\Entity\ClubUser;
use KI\UserBundle\Form\ClubUserType;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClubsController extends SubresourceController
{
    public function setContainer(ContainerInterface $container = null)
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
     * @Route("/clubs")
     * @Method("GET")
     */
    public function getClubsAction()
    {
        return $this->getAll($this->is('EXTERIEUR'));
    }

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
     * @Route("/clubs/{slug}")
     * @Method("GET")
     */
    public function getClubAction($slug)
    {
        $club = $this->getOne($slug, $this->is('EXTERIEUR'));

        return $this->json($club);
    }

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
     * @Route("/clubs")
     * @Method("POST")
     */
    public function postClubAction()
    {
        return $this->post();
    }

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
     * @Route("/clubs/{slug}")
     * @Method("PATCH")
     */
    public function patchClubAction($slug)
    {
        return $this->patch(
            $slug,
            $this->isClubMember($slug)
            && (!$this->get('security.authorization_checker')->isGranted('ROLE_EXTERIEUR')
                || $slug == $this->get('security.token_storage')->getToken()->getUser()->getSlug()
            )
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
     * @Route("/clubs/{slug}")
     * @Method("DELETE")
     */
    public function deleteClubAction($slug)
    {
        $repoLink = $this->manager->getRepository('KIUserBundle:ClubUser');
        $club = $this->findBySlug($slug);
        $link = $repoLink->findBy(['club' => $club]);

        foreach ($link as $clubUser) {
            $this->manager->remove($clubUser);
        }

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
     * @Route("/clubs/{slug}/users")
     * @Method("GET")
     */
    public function getClubUsersAction($slug)
    {
        $members =  $this->getAllSub($slug, 'User', false);

        return $this->json($members);
    }

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
     * @Route("/clubs/{slug}/users/{id}")
     * @Method("POST")
     */
    public function postClubUserAction(Request $request, $slug, $id)
    {
        $this->trust($this->is('ADMIN') || $this->isClubMember($slug));

        // On récupère les deux entités concernées
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($id);
        $club = $this->findBySlug($slug);

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->manager->getRepository('KIUserBundle:ClubUser');
        $link = $repoLink->findBy(['club' => $club, 'user' => $user]);

        // On crée la relation si elle n'existe pas déjà
        if (count($link) == 0) {
            // Création de l'entité relation
            $link = new ClubUser();
            $link->setClub($club);
            $link->setUser($user);
            $link->setPriority($user->getId());

            // Validation des données annexes
            $form = $this->createForm(ClubUserType::class, $link, ['method' => 'POST']);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->manager->persist($link);
                $this->manager->flush();

                return $this->json(null, 204);
            } else {
                $this->manager->detach($link);
                return $this->json($form, 400);
            }
        } else
            throw new BadRequestHttpException('La relation entre Club et User existe déjà');
    }

    /**
     * @ApiDoc(
     *  description="Modifie un membre d'un club",
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
     * @Route("/clubs/{slug}/users/{username}")
     * @Method("PATCH")
     */
    public function patchClubUserAction(Request $request, $slug, $username)
    {
        $this->trust($this->is('ADMIN') || $this->isClubMember($slug));

        // On récupère les deux entités concernées
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($username);
        $club = $this->findBySlug($slug);

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->manager->getRepository('KIUserBundle:ClubUser');
        $link = $repoLink->findBy(['club' => $club, 'user' => $user]);

        // On édite la relation si elle existe (de façon unique)
        if (count($link) == 1) {
            $link = $link[0];
            // Validation des données annexes
            $form = $this->createForm(ClubUserType::class, $link, ['method' => 'PATCH']);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->manager->persist($link);
                $this->manager->flush();

                return $this->json(null, 204);
            } else {
                $this->manager->detach($link);
                return $this->json($form, 400);
            }
        } else
            throw new BadRequestHttpException('Cette personne ne fait pas partie du club !');
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
     * @Route("/clubs/{slug}/users/{id}")
     * @Method("DELETE")
     */
    public function deleteClubUserAction($slug, $id)
    {
        if (!($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN') || $this->isClubMember($slug)))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($id);
        $club = $this->findBySlug($slug);

        // On récupère la relation
        $repoLink = $this->manager->getRepository('KIUserBundle:ClubUser');
        $link = $repoLink->findBy(['club' => $club, 'user' => $user]);

        // Supprime la relation si elle existe
        if (count($link) == 1) {
            $this->manager->remove($link[0]);
            $this->manager->flush();
        } else
            throw new NotFoundHttpException('Relation entre Club et User non trouvée');
        return $this->json(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Echange les priorité des 2 clubUsers associés aux Users",
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
     * @Route("/clubs/{slug}/users/{username}/{direction}")
     * @Method("PATCH")
     */
    public function swapPriorityClubUserAction($slug, $username, $direction)
    {
        $this->trust($this->is('ADMIN') || $this->isClubMember($slug));

        // On récupère les entités concernées
        $repo = $this->manager->getRepository('KIUserBundle:User');
        $user = $repo->findOneByUsername($username);
        $club = $this->findBySlug($slug);

        // Trouve les clubUsers assiciés aux Users
        $repoLink = $this->manager->getRepository('KIUserBundle:ClubUser');
        $link = $repoLink->findOneBy(['club' => $club, 'user' => $user]);

        $priority = $link->getPriority();
        $promo = $user->getPromo();

        if ($direction == 'down') {
            $auDessous = $this->manager->createQuery('SELECT cu
                FROM KIUserBundle:ClubUser cu,
                KIUserBundle:User user
                WHERE cu.club = :club
	            AND cu.user = user
                AND user.promo = :promo
                AND cu.priority > :priority
                ORDER BY cu.priority ASC')
                ->setParameter('club', $club)
                ->setParameter('priority', $priority)
                ->setParameter('promo', $promo)
                ->setMaxResults(1)
                ->getSingleResult();

            // On édite les clubUsers
            $link->setPriority($auDessous->getPriority());
            $auDessous->setPriority($priority);
            $this->manager->flush();
        } else if ($direction == 'up') {
            $auDessus = $this->manager->createQuery('SELECT cu
                FROM KIUserBundle:ClubUser cu,
                KIUserBundle:User user
                WHERE cu.club = :club
	            AND cu.user = user
                AND user.promo = :promo
                AND cu.priority < :priority
                ORDER BY cu.priority DESC')
                ->setParameter('club', $club)
                ->setParameter('priority', $priority)
                ->setParameter('promo', $promo)
                ->setMaxResults(1)
                ->getSingleResult();

            // On édite les clubUsers
            $link->setPriority($auDessus->getPriority());
            $auDessus->setPriority($priority);
            $this->manager->flush();
        } else
            throw new BadRequestHttpException('Direction invalide');
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
     * @Route("/clubs/{slug}/follow")
     * @Method("POST")
     */
    public function followClubAction($slug)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $club = $this->findBySlug($slug);

        if (!$user->getClubsNotFollowed()->contains($club)) {
            throw new BadRequestHttpException('Vous êtes déjà abonné à ce club');
        } else {
            $user->removeClubNotFollowed($club);
            $this->manager->flush();

            return $this->json(null, 204);
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
     * @Route("/clubs/{slug}/unfollow")
     * @Method("POST")
     */
    public function unFollowClubAction($slug)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $club = $this->findBySlug($slug);

        if ($user->getClubsNotFollowed()->contains($club)) {
            throw new BadRequestHttpException('Vous n\'êtes pas abonné à ce club');
        } else {
            $user->addClubNotFollowed($club);
            $this->manager->flush();

            return $this->json(null, 204);
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
     * @Route("/clubs/{slug}/newsitems")
     * @Method("GET")
     */
    public function getNewsitemsClubAction($slug)
    {
        $repository = $this->manager->getRepository('KIPublicationBundle:Newsitem');

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($repository));

        $findBy['authorClub'] = $this->findBySlug($slug);
        $results = $repository->findBy($findBy, $sortBy, $limit, $offset);
        return $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);
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
     * @Route("/clubs/{slug}/events")
     * @Method("GET")
     */
    public function getEventsClubAction($slug)
    {
        $repository = $this->manager->getRepository('KIPublicationBundle:Event');

        $paginateHelper = $this->get('ki_core.helper.paginate');
        extract($paginateHelper->paginateData($repository));

        $findBy['authorClub'] = $this->findBySlug($slug);
        $results = $repository->findBy($findBy, $sortBy, $limit, $offset);
        return $paginateHelper->paginateView($results, $limit, $page, $totalPages, $count);
    }
}
