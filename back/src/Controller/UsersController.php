<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\User;
use App\Event\AchievementCheckEvent;
use App\Factory\UserFactory;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\CalendarService;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UsersController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Liste les utilisateurs",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/users", methods={"GET"})
     */
    public function getUsersAction()
    {
        return $this->getAll();
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne un utilisateur",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/users/{slug}", methods={"GET"})
     */
    public function getUserAction($slug)
    {
        $user = $this->getOne($slug, true);

        return $this->json($user);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Modifie un utilisateur",
     *     @SWG\Parameter(
     *         name="email",
     *         in="formData",
     *         description="form.email",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="username",
     *         in="formData",
     *         description="form.username",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="plainPassword",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="gender",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="firstName",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="lastName",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="nickname",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="promo",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="department",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="origin",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="nationality",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="location",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="skype",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="statsFoyer",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="statsPonthub",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="statsFacegame",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="allowedBde",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="allowedBds",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="tour",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="image",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="mailEvent",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="mailModification",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Parameter(
     *         name="mailShotgun",
     *         in="formData",
     *         description="",
     *         required=false,
     *         type="boolean",
     *     ),
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="La syntaxe de la requête est erronée"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/users/{slug}", methods={"PATCH"})
     */
    public function patchUserAction(Request $request, $slug)
    {
        // Les admissibles et extérieurs ne peuvent pas modifier leur profil
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMISSIBLE')
            || $this->get('security.authorization_checker')->isGranted('ROLE_EXTERIEUR')
        )
            throw new AccessDeniedException();

        if ($request->request->has('image')) {
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch(new AchievementCheckEvent(Achievement::PHOTO));
        }

        // Un utilisateur peut se modifier lui même
        $user = $this->getUser();
        $patchData = $this->patch($slug, $user->getUsername() == $slug);

        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(new AchievementCheckEvent(Achievement::PROFILE));

        if ($request->query->has('achievement')) {
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch(new AchievementCheckEvent(Achievement::TOUR));
        }

        return $this->formJson($patchData);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Supprime un utilisateur",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/users/{slug}", methods={"DELETE"})
     */
    public function deleteUserAction($slug)
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }
        $userManager = $this->get('fos_user.user_manager');
        $user = $this->findBySlug($slug);
        $userManager->deleteUser($user);

        return $this->json(null, 204);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Récupère la liste des clubs dont l'utilisateur est membre",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/users/{slug}/clubs", methods={"GET"})
     */
    public function getUserClubsAction($slug)
    {
        $user = $this->findBySlug($slug);

        $clubs = $this->repository->getUserClubs($user);

        return $this->json($clubs, 200);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne le calendrier de l'utilisateur au format ICS",
     *     @SWG\Response(
     *         response="200",
     *         description="Requête traitée avec succès"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Une authentification est nécessaire pour effectuer cette action"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Pas les droits suffisants pour effectuer cette action"
     *     )
     * )
     *
     * @Route("/users/{token}/calendar", methods={"GET"})
     */
    public function getUserCalendarAction(CalendarService $calendarService, User $user, UserRepository $userRepository)
    {
        $events = $userRepository->findFollowedEvents($user);
        // $courses = $this->getCourseitems($user);
        // FIXME

        $calStr = $calendarService->getCalendar($user, $events, []);

        return new Response($calStr, 200, [
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="calendar.ics"',
            ]
        );
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Crée un compte et envoie un mail avec le mot de passe",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     )
     * )
     *
     * @Route("/users", methods={"POST"})
     */
    public function postUsersAction(UserFactory $userFactory, Request $request)
    {
        //On limite la création de compte aux admins
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        if (!$request->request->has('firstName') || !$request->request->has('lastName') || !$request->request->has('email'))
            throw new BadRequestHttpException('Champs non rempli(s)');

        $lastName = $request->request->get('lastName');
        $firstName = $request->request->get('firstName');
        $email = $request->request->get('email');

        if (!preg_match('/@eleves\.enpc\.fr$/', $email)) ///@(eleves\.)?enpc\.fr$/
            throw new BadRequestHttpException('Adresse mail non utilisable');

        // On check si l'utilisateur n'existe pas déjà
        $repo = $this->manager->getRepository(User::class);
        $users = $repo->findByEmail($email);

        if (count($users) > 0)
            throw new BadRequestHttpException('Cet utilisateur existe déjà.');

        // Si le login existe déjà, on ajoute une lettre du prénom
        $login = strtolower(str_replace(' ', '-', substr($this->stripAccents($lastName), 0, 7) . $this->stripAccents($firstName)[0]));
        $i = 1;
        while (count($repo->findByUsername($login)) > 0) {
            if (isset($firstName[$i]))
                $login .= $firstName[$i];
            else
                $login .= '1';
            $i++;
        }

        $attributes = [
            'username' => $login,
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ];

        $userFactory->createUser($login, [], $attributes);

        return $this->json(null, 201);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Crée un compte et envoie un mail avec le mot de passe",
     *     @SWG\Response(
     *         response="201",
     *         description="Requête traitée avec succès avec création d’un document"
     *     )
     * )
     *
     * @Route("/import/users", methods={"POST"})
     */
    public function importUsersAction(UserFactory $userFactory, Request $request)
    {
        set_time_limit(3600);
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            return $this->json(null, 403);

        if (!$request->files->has('users'))
            throw new BadRequestHttpException('Aucun fichier envoyé');
        $file = $request->files->get('users');

        // Check CSV
        if ($file->getMimeType() !== 'text/plain' && $file->getMimeType() !== 'text/csv') {
            throw new Exception('L\import doit se faire au moyen d\'un fichier CSV');
        }

        // On récupère le contenu du fichier
        $path = __DIR__ . '/../../public/uploads/tmp/';
        $file->move($path, 'users.list');
        $list = fopen($path . 'users.list', 'r+');
        if ($list === false)
            throw new BadRequestHttpException('Erreur lors de l\'upload du fichier');

        // Dans un premier temps on va effectuer une première passe pour vérifier qu'il n'y a pas de duplicatas
        $fails = $success = [];

        while (!feof($list)) {
            // On enlève le caractère de fin de ligne
            $line = str_replace(["\r", "\n"], ['', ''], fgets($list));
            if (empty($line))
                continue;

            $gender = $login = $firstName = $lastName = $email = $promo = $department = $origin = null;
            $explode = explode(',', $line);
            list($gender, $lastName, $firstName, $email, $origin, $department, $promo) = $explode;
            $firstName = ucfirst($firstName);
            $lastName = ucwords(mb_strtolower($lastName));

            $login = explode('@', $email)[0];

            $e = [];
            if (!preg_match('/@(eleves\.)?enpc\.fr$/', $email))
                $e[] = 'Adresse mail non utilisable';

            if (count($e) > 0) {
                $fails[] = $line . ' : ' . implode(', ', $e);
            } else {

                /**
                 * @var $user User
                 */
                $user = $this->repository->findOneBy(['email' => $email]);
                if (!$user) {
                    $attributes = [
                        'username' => $login,
                        'email' => $email,
                        'loginMethod' => 'form',
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'promo' => $promo,
                        'department' => $department,
                        'origin' => $origin,
                    ];

                    $user = $userFactory->createUser($login, [], $attributes);
                } else {
                    $user->setPromo($promo);
                    $user->setDepartment($department);
                    $user->setOrigin($origin);
                }
                $user->setGender($gender);

                $success[] = $firstName . ' ' . $lastName;
            }
        }

        return $this->json([
            'success' => $success,
            'fails' => $fails,
        ], 201);
    }

    private function stripAccents($string)
    {
        return str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'],
            ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'],
            $string);
    }
}
