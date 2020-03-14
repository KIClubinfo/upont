<?php

namespace App\Security;

use App\Entity\Achievement;
use App\Event\AchievementCheckEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

abstract class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtManager;
    private $dispatcher;

    public function __construct(JWTTokenManagerInterface $jwtManager, EventDispatcherInterface $dispatcher)
    {
        $this->jwtManager = $jwtManager;
        $this->dispatcher = $dispatcher;
    }

    public function supports(Request $request)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => $exception->getMessageKey()
        ];

        return new JsonResponse($data, 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $user = $token->getUser();

        // On commence par checker éventuellement l'achievement de login
        $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::LOGIN));

        $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::SPIRIT));

        $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::KIEN));

        $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::ADMIN));


        $balance = $user->getBalance();
        if ($balance !== null) {
            if ($balance < 0) {
                $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::FOYER));
            } else {
                $this->dispatcher->dispatch(new AchievementCheckEvent(Achievement::FOYER_BIS));
            }
        }

        $data = [
            'code' => 200,
            'data' => [
                'username' => $user->getUsername(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'roles' => $user->getRoles(),
                //'first' => $event->getRequest()->request->has('first'),
            ],
            'token' => $this->jwtManager->create($user),
        ];

        return new JsonResponse($data, 200);
    }

    public function start(Request $request, AuthenticationException $e = null)
    {
        $data = [
            'message' => 'Missing credentials.',
            'code' => 401,
        ];

        return new JsonResponse($data, 401);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
