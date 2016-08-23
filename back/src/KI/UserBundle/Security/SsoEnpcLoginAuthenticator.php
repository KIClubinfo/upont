<?php

namespace KI\UserBundle\Security;

use KI\UserBundle\Factory\UserFactory;
use KI\UserBundle\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Security\Core\User\UserProviderInterface;


class SsoEnpcLoginAuthenticator extends LoginAuthenticator
{
    private $userFactory;
    private $userRepository;
    private $proxyUrl;
    private $proxyUser;

    public function __construct(
        JWTManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        UserFactory $userFactory,
        UserRepository $userRepository,
        $proxyUrl,
        $proxyUser
    )
    {
        parent::__construct($jwtManager, $dispatcher);

        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->proxyUrl = $proxyUrl;
        $this->proxyUser = $proxyUser;
    }

    public function getCredentials(Request $request)
    {
        ob_start();
        try {
            \phpCAS::setDebug();
            \phpCAS::setVerbose(true);
            \phpCAS::client(SAML_VERSION_1_1, 'cas.enpc.fr', 443, '/cas');
            \phpCAS::setNoCasServerValidation();
            \phpCAS::setExtraCurlOption(CURLOPT_PROXY, $this->proxyUrl);
            \phpCAS::setExtraCurlOption(CURLOPT_PROXYUSERPWD, $this->proxyUser);
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

        $email = $credentials['mail'];
        if(!preg_match('/@eleves\.enpc\.fr$/', $email))
            throw new AccessDeniedException();

        $user = null;

        try {
            $user = $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $exception) {

        }

        if(!$user) {
            $user = $this->userRepository->findOneBy(['email' => $credentials['mail']]);
        }

        if (!$user) {
            $credentials = [
                'username' => $username,
                'firstName' => $credentials['givenName'],
                'lastName' => ucwords(strtolower($credentials['sn'])),
                'email' => $credentials['mail'],
                'promo' => '0' . ( substr(strftime('%Y'), 2) + 3)
            ];

            $user = $this->userFactory->createUser($username, [], $credentials);
        }


        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }
}
