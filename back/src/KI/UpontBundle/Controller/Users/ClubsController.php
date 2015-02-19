<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\UpontBundle\Form\Users\ClubUserType;
use KI\UpontBundle\Entity\Users\ClubUser;

class ClubsController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Club', 'Users');
    }



    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les clubs",
     *  output="KI\UpontBundle\Entity\Users\Club",
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
     *  output="KI\UpontBundle\Entity\Users\Club",
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
     *  input="KI\UpontBundle\Form\Users\ClubType",
     *  output="KI\UpontBundle\Entity\Users\Club",
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
     *  input="KI\UpontBundle\Form\Users\ClubType",
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
    public function patchClubAction($slug) { return $this->patch($slug); }

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
    public function deleteClubAction($slug) { return $this->delete($slug); }

    /**
     * @ApiDoc(
     *  description="Liste les membres d'un club",
     *  output="KI\UpontBundle\Entity\Users\User",
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
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $user = $repo->findOneByUsername($id);
        $club = $this->findBySlug($slug);

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->em->getRepository('KIUpontBundle:Users\ClubUser');
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
            }
            else {
                $this->em->detach($link);
                return $this->jsonResponse($form, 400);
            }
        }
        else
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
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException('Accès refusé');

        // On récupère les deux entités concernées
        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $user = $repo->findOneByUsername($id);
        $club = $this->findBySlug($slug);

        // On récupère la relation
        $repoLink = $this->em->getRepository('KIUpontBundle:Users\ClubUser');
        $link = $repoLink->findBy(array('club' => $club, 'user' => $user));

        // Supprime la relation si elle existe
        if (count($link) == 1) {
            $this->em->remove($link[0]);
            $this->em->flush();
        }
        else
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
     *  description="Retourne toutes les publications réalisées par un club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/clubs/{slug}/publications")
     */
    public function getPublicationsClubAction($slug)
    {
        $club = $this->findBySlug($slug);
        $repo = $this->em->getRepository('KIUpontBundle:Publications\Post');
        $posts = $repo->findByAuthorClub($club);

        return $posts;
    }
}
