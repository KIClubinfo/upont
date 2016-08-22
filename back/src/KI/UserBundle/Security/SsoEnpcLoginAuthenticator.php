<?php

namespace KI\UserBundle\Security;

use KI\UserBundle\Factory\UserFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Core\User\UserProviderInterface;


class SsoEnpcLoginAuthenticator extends LoginAuthenticator
{
    private $userFactory;
    private $proxyUrl;
    private $proxyUser;

    public function __construct(
        JWTManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        UserFactory $userFactory,
        $proxyUrl,
        $proxyUser
    )
    {
        parent::__construct($jwtManager, $dispatcher);

        $this->userFactory = $userFactory;
        $this->proxyUrl = $proxyUrl;
        $this->proxyUser = $proxyUser;
    }

    public function getCredentials(Request $request)
    {
        ob_start();
        try {
            \phpCAS::setDebug();
            \phpCAS::setVerbose(true);
            \phpCAS::client(CAS_VERSION_2_0, 'cas.enpc.fr', 443, '/cas');
            \phpCAS::setNoCasServerValidation();
            \phpCAS::handleLogoutRequests();
//            \phpCAS::setCacheTimesForAuthRecheck(0);
            \phpCAS::setFixedServiceURL('https://upont.enpc.fr');
            \phpCAS::setExtraCurlOption(CURLOPT_PROXY, $this->proxyUrl);
            \phpCAS::setExtraCurlOption(CURLOPT_PROXYUSERPWD, $this->proxyUser);
//            \phpCAS::logout();
            \phpCAS::setNoClearTicketsFromUrl();
            \phpCAS::forceAuthentication();
        } catch (\CAS_AuthenticationException $exception) {
            throw new CustomUserMessageAuthenticationException('CAS authentication exception');
        } finally {
            ob_end_clean();
        }
        return array_merge([
            'username' => \phpCAS::getUser(),
            'validateUrl' => \phpCAS::getServiceURL(),
        ], \phpCAS::getAttributes());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];

        $user = null;

        $user = $userProvider->loadUserByUsername($username);


//        try {
//            $user = $userProvider->loadUserByUsername($username);
//        } catch (UsernameNotFoundException $exception) {
//
//        }

//        throw new BadCredentialsException(print_r($credentials));

//        if (!$user) {
//            //FIXME
//            $credentials = array_merge($credentials, [
//                'firstName' => 'Louis',
//                'lastName' => 'Trezzini',
//                'email' => 'louis.trezzini@eleves.enpc.fr',
//                'password' => '',
//                'loginMethod' => 'cas'
//            ]);
//
//            $user = $this->userFactory->createUser($username, [], $credentials);
//        }


        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
}
