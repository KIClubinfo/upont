<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use KI\UpontBundle\Entity\Users\Achievement;
use KI\UpontBundle\Event\AchievementCheckEvent;

class UsersController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'Users');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les utilisateurs",
     *  output="KI\UpontBundle\Entity\Users\User",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getUsersAction() { return $this->getAll(); }

    /**
     * @ApiDoc(
     *  description="Retourne un utilisateur",
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
    public function getUserAction($slug) { return $this->getOne($slug, true); }

    /**
     * @ApiDoc(
     *  description="Crée un utilisateur",
     *  input="KI\UpontBundle\Form\Users\UserType",
     *  output="KI\UpontBundle\Entity\Users\User",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postUsersAction() { return $this->post(); }

    /**
     * @ApiDoc(
     *  description="Modifie un utilisateur",
     *  input="KI\UpontBundle\Form\Users\UserType",
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
    public function patchUserAction($slug)
    {
        // Les admissibles et extérieurs ne peuvent pas modifier leur profil
        if ($this->get('security.context')->isGranted('ROLE_ADMISSIBLE')
            || $this->get('security.context')->isGranted('ROLE_EXTERIEUR'))
            throw new AccessDeniedException();

        $request = $this->getRequest()->request;
        if ($request->has('image')) {
            $dispatcher = $this->container->get('event_dispatcher');
            $achievementCheck = new AchievementCheckEvent(Achievement::PHOTO);
            $dispatcher->dispatch('upont.achievement', $achievementCheck);
        }

        // Un utilisateur peut se modifier lui même
        $user = $this->get('security.context')->getToken()->getUser();
        $response = $this->patch($slug, $user->getUsername() == $slug);

        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::PROFILE);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        return $response;
    }

    /**
     * @ApiDoc(
     *  description="Supprime un utilisateur",
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
    public function deleteUserAction($slug)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->findBySlug($slug);
        $userManager->deleteUser($user);
    }

    /**
     * @ApiDoc(
     *  description="Récupère la liste des clubs dont l'utilisateur est membre",
     *  output="KI\UpontBundle\Entity\Users\Club",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/users/{slug}/clubs")
     */
    public function getUserClubsAction($slug)
    {
        $clubs = array();
        $user = $this->findBySlug($slug);
        $repo = $this->em->getRepository('KIUpontBundle:Users\ClubUser');
        $clubUsers = $repo->findByUser($user);

        foreach ($clubUsers as $clubUser) {
            $clubs[] = array(
                'club' => $clubUser->getClub(),
                'role' => $clubUser->getRole()
            );
        }

        return $this->restResponse($clubs, 200);
    }
}
