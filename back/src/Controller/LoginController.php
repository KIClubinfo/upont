<?php

namespace App\Controller;

use App\Controller\BaseController;
use App\Entity\Achievement;
use App\Entity\Club;
use App\Entity\User;
use App\Event\AchievementCheckEvent;
use App\Form\UserType;
use App\Service\TokenService;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Swift_Message;

class LoginController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @ApiDoc(
     *  description="Retourne les utilisateurs étant connectés et si le KI est ouvert",
     *  parameters={
     *   {
     *    "name"="delay",
     *    "dataType"="integer",
     *    "required"=false,
     *    "description"="Temps de l'intervalle considéré en minutes (30 minutes par défaut)"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route("/refresh", methods={"GET"})
     */
    public function refreshAction(Request $request)
    {
        $delay = $request->query->has('delay') ? (int)$request->query->get('delay') : 30;
        $clubRepo = $this->manager->getRepository(Club::class);

        return $this->json([
                                'online' => $this->repository->getOnlineUsers($delay),
                                'open' => $clubRepo->findOneBySlug('ki')->getOpen()
                           ]);
    }


    /**
     * @ApiDoc(
     *  description="Envoie un mail permettant de reset le mot de passe",
     *  requirements={
     *   {
     *    "name"="username",
     *    "dataType"="string",
     *    "description"="Le nom d'utilisateur"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Mauvaise combinaison username/password ou champ nom rempli",
     *   404="Ressource non trouvée",
     *  },
     *  section="Général"
     * )
     * @Route("/resetting/request", methods={"POST"})
     */
    public function resettingAction(TokenService $tokenService, Request $request)
    {
        if (!$request->request->has('username'))
            throw new BadRequestHttpException('Aucun nom d\'utilisateur fourni');

        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository(User::class);
        $user = $repo->findOneByUsername($request->request->get('username'));

        if ($user) {
            if ($user->hasRole('ROLE_ADMISSIBLE'))
                return $this->json(null, 403);

            $token = $tokenService->getToken($user);
            $message = (new Swift_Message('Réinitialisation du mot de passe'))
                ->setFrom('noreply@upont.enpc.fr')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('resetting.txt.twig', ['token' => $token, 'name' => $user->getFirstName()]));
            $this->get('mailer')->send($message);

            $dispatcher = $this->container->get('event_dispatcher');
            $achievementCheck = new AchievementCheckEvent(Achievement::PASSWORD, $user);
            $dispatcher->dispatch('upont.achievement', $achievementCheck);

            return $this->json(null, 204);
        } else
            throw new NotFoundHttpException('Utilisateur non trouvé');
    }

    /**
     * @ApiDoc(
     *  description="Reset son mot de passe à partir du mail",
     *  requirements={
     *   {
     *    "name"="password",
     *    "dataType"="string",
     *    "description"="Le mot de passe"
     *   },
     *   {
     *    "name"="check",
     *    "dataType"="string",
     *    "description"="Le mot de passe une seconde fois (confirmation)"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Mauvaise combinaison username/password ou champ nom rempli",
     *   404="Ressource non trouvée",
     *  },
     *  section="Général"
     * )
     * @Route("/resetting/token/{token}", methods={"POST"})
     */
    public function resettingTokenAction(Request $request, $token)
    {
        if (!$request->request->has('password') || !$request->request->has('check'))
            throw new BadRequestHttpException('Champs password/check non rempli(s)');

        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository(User::class);
        $user = $repo->findOneByToken($token);

        if ($user) {
            if ($user->hasRole('ROLE_ADMISSIBLE'))
                return $this->json(null, 403);

            $username = $user->getUsername();

            // Pour changer le mot de passe on doit passer par le UserManager
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserByUsername($username);


            if ($request->request->get('password') != $request->request->get('check'))
                throw new BadRequestHttpException('Mots de passe non identiques');

            $user->setPlainPassword($request->request->get('password'));
            $userManager->updateUser($user, true);

            return $this->json(null, 204);
        } else
            throw new NotFoundHttpException('Utilisateur non trouvé');
    }
}
