<?php

namespace App\Controller;

use App\Entity\Achievement;
use App\Entity\User;
use App\Event\AchievementCheckEvent;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\TokenService;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Swagger\Annotations as SWG;
use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends BaseController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize(User::class, UserType::class);
    }

    /**
     * @Operation(
     *     tags={"Utilisateurs"},
     *     summary="Retourne les utilisateurs étant connectés",
     *     @SWG\Parameter(
     *         name="delay",
     *         in="body",
     *         description="Temps de l'intervalle considéré en minutes (30 minutes par défaut)",
     *         required=false,
     *         type="integer",
     *         schema=""
     *     ),
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
     * @Route("/refresh", methods={"GET"})
     */
    public function refreshAction(Request $request, UserRepository $userRepository)
    {
        $delay = $request->query->has('delay') ? (int)$request->query->get('delay') : 30;

        return $this->json([
            'online' => $userRepository->getOnlineUsers($delay)
        ]);
    }


    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Envoie un mail permettant de reset le mot de passe",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Mauvaise combinaison username/password ou champ nom rempli"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/resetting/request", methods={"POST"})
     */
    public function resettingAction(TokenService $tokenService, Request $request, UserRepository $userRepository, EventDispatcherInterface $eventDispatcher)
    {
        if (!$request->request->has('username'))
            throw new BadRequestHttpException('Aucun nom d\'utilisateur fourni');

        $user = $userRepository->findOneByUsername($request->request->get('username'));

        if ($user) {
            if ($user->hasRole('ROLE_ADMISSIBLE'))
                return $this->json(null, 403);

            $token = $tokenService->getToken($user);
            $message = (new Swift_Message('Réinitialisation du mot de passe'))
                ->setFrom('noreply@upont.enpc.fr')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('resetting.txt.twig', ['token' => $token, 'name' => $user->getFirstName()]));
            $this->get('mailer')->send($message);

            $achievementCheck = new AchievementCheckEvent(Achievement::PASSWORD, $user);
            $eventDispatcher->dispatch('upont.achievement', $achievementCheck);

            return $this->json(null, 204);
        } else
            throw new NotFoundHttpException('Utilisateur non trouvé');
    }

    /**
     * @Operation(
     *     tags={"Général"},
     *     summary="Reset son mot de passe à partir du mail",
     *     @SWG\Response(
     *         response="204",
     *         description="Requête traitée avec succès mais pas d’information à renvoyer"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Mauvaise combinaison username/password ou champ nom rempli"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Ressource non trouvée"
     *     )
     * )
     *
     * @Route("/resetting/token/{token}", methods={"POST"})
     */
    public function resettingTokenAction(Request $request, $token, UserRepository $userRepository)
    {
        if (!$request->request->has('password') || !$request->request->has('check'))
            throw new BadRequestHttpException('Champs password/check non rempli(s)');

        $user = $userRepository->findOneByToken($token);

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
