<?php

namespace KI\UserBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use KI\UserBundle\Factory\UserFactory;


class SsoEnpcLoginAuthenticator extends LoginAuthenticator
{
    private $userFactory;

    public function __construct(
        JWTManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        UserFactory $userFactory
    )
    {
        parent::__construct($jwtManager, $dispatcher);

        $this->userFactory = $userFactory;
    }

    public function getCredentials(Request $request)
    {

        \phpCAS::setDebug();
        \phpCAS::setVerbose(true);
        \phpCAS::client(CAS_VERSION_2_0, 'cas.enpc.fr', 443, '/cas');
        \phpCAS::setNoCasServerValidation();
        \phpCAS::handleLogoutRequests();
        \phpCAS::forceAuthentication();
        return array_merge([
            'username' => \phpCAS::getUser(),
            'validateUrl' => \phpCAS::getServiceURL(),
        ], \phpCAS::getAttributes());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];

        $user = null;

        try {
            $user = $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $exception) {

        }

        if (!$user) {
            //FIXME
            $credentials = array_merge($credentials, [
                'firstName' => 'Louis',
                'lastName' => 'Trezzini',
                'email' => 'louis.trezzini@eleves.enpc.fr',
                'password' => '',
                'loginMethod' => 'cas'
            ]);
//            throw new BadCredentialsException(print_r($credentials));

            $user = $this->userFactory->createUser($username, [], $credentials);
        }


        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
}
