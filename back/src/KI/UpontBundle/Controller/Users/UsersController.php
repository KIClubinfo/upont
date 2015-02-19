<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function getUserAction($slug) { return $this->getOne($slug); }

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
    public function patchUserAction($slug) { return $this->patch($slug); }

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

        foreach($clubUsers as $clubUser) {
            $clubs[] = array(
                'club' => $clubUser->getClub(),
                'role' => $clubUser->getRole()
            );
        }

        return $this->restResponse($clubs, 200);
    }

    /**
     * @ApiDoc(
     *  description="Retourne le calendrier de l'utilisateur dont le token correpsond",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Aucun résultat ne correspond au token transmis",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/users/{token}/calendar")
     */
    public function getCalendarAction($token)
    {
        $user = $this->repo->findOneByToken($token);
        if ($user == null) {
            throw new NotFoundHttpException('Aucun utilisateur ne correspond au token saisi');
        } else {
            $calStr = $this->get('ki_upont.calendar')->getCalendar($user);

            return new \Symfony\Component\HttpFoundation\Response($calStr, 200, array(
                    'Content-Type' => 'text/calendar; charset=utf-8',
                    'Content-Disposition' => 'attachment; filename="calendar.ics"',
                )
            );
        }
    }

    /**
     * @ApiDoc(
     *  description="Retourne un tableau de données pour le jeu",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/promo_game")
     */
    public function getPromoGameAction()
    {
        $maxId = $this->em->createQuery('SELECT MAX(u.id) FROM KIUpontBundle:Users\User u')->getSingleScalarResult();
        $query = $this->em->createQuery('SELECT u FROM KIUpontBundle:Users\User u WHERE u.id >= :rand ORDER BY u.id ASC');
        $rand1 = rand(0,$maxId);

        do{
            $rand2 = rand(0, $maxId);
        } while($rand1 == $rand2);

        do{
            $rand3 = rand(0, $maxId);
        } while($rand3 == $rand2 || $rand3 == $rand1);

        return $this->jsonResponse(array("user1" => $query->setParameter('rand',$rand1)->setMaxResults(1)->getSingleResult()->getFirstName(),
            "user2" => $query->setParameter('rand',$rand2)->setMaxResults(1)->getSingleResult()->getFirstName(),
            "user3" => $query->setParameter('rand',$rand3)->setMaxResults(1)->getSingleResult()->getFirstName()
        ),200);
    }
}
