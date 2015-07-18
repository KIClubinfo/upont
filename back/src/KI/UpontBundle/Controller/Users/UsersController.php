<?php

namespace KI\UpontBundle\Controller\Users;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

    /**
     * @ApiDoc(
     *  description="Crée un compte et envoie un mail avec le mot de passe",
     *  requirements={
     *   {
     *    "name"="firstName",
     *    "dataType"="string",
     *    "description"="Prénom"
     *   },
     *   {
     *    "name"="lastName",
     *    "dataType"="string",
     *    "description"="Nom"
     *   },
     *   {
     *    "name"="email",
     *    "dataType"="string",
     *    "description"="Adresse email"
     *   },
     *  },
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function postUsersAction()
    {
        $request = $this->getRequest()->request;
        if (!$request->has('firstName') || !$request->has('lastName')  || !$request->has('email'))
            throw new BadRequestHttpException('Champs non rempli(s)');

        $lastName = $request->get('lastName');
        $firstName = $request->get('firstName');
        $email = $request->get('email');

        if (!preg_match('/@(eleves\.)?enpc\.fr$/', $email))
            throw new BadRequestHttpException('Adresse mail non utilisable');

        // On check si l'utilisateur n'existe pas déjà
        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $users = $repo->findByEmail($email);

        if (count($users) > 0)
            throw new BadRequestHttpException('Cet utilisateur existe déjà.');

        // Generation du mot de passe
        $password = substr(str_shuffle(strtolower(sha1(rand().time().'salt'))), 0, 8);
        $login = strtolower(str_replace(' ', '-', substr($this->stripAccents($lastName), 0, 7).$this->stripAccents($firstName)[0]));

        // Si le login existe déjà, on ajoute une lettre du prénom
        $i = 1;
        while (count($repo->findByUsername($login)) > 0) {
            if (isset($firstName[$i]))
                $login .= $firstName[$i];
            else
                $login .= '1';
            $i++;
        }

        // Création de l'utilisateur
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setUsername($login);
        $user->setEmail($email);
        $user->setEnabled(true);
        $user->setPlainPassword($password);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $userManager->updateUser($user);

        // Envoi du mail
        $message = \Swift_Message::newInstance()
            ->setSubject('Inscription uPont')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo($email)
            ->setBody($this->renderView('KIUpontBundle::registration.txt.twig', array('firstName' => $firstName, 'login' => $login, 'password' => $password)));
        $this->get('mailer')->send($message);

        $message = \Swift_Message::newInstance()
            ->setSubject('[uPont] Nouvelle inscription ('.$login.')')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo('root@clubinfo.enpc.fr')
            ->setBody($this->renderView('KIUpontBundle::registration-ki.txt.twig', array('firstName' => $firstName, 'lastName' => $lastName, 'login' => $login, 'email' => $email)));
        $this->get('mailer')->send($message);

        return $this->restResponse(null, 201);
    }

    private function stripAccents($string){
        return str_replace(
            array('à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'),
            array('a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'),
            $string);
    }
}
