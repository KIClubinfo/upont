<?php


namespace App\Security;

use App\OidcClient\OidcClientSettings;
use App\OidcClient\Service\ValidationService;
use Exception;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;

class KIAuthIdTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    protected $validationService;

    public function __construct()
    {
        $validationService = new ValidationService(new OidcClientSettings([
            'authority' => 'http://localhost:4444'
        ]));
        $this->validationService = $validationService;
    }

    public function createToken(Request $request, $providerKey)
    {
        // look for an authorization header
        $authorizationHeader = $request->headers->get('Authorization');

        if (empty($authorizationHeader)) {
            throw new BadCredentialsException();
        }

        // extract the JWT
        $idToken = str_replace('Bearer ', '', $authorizationHeader);

        // decode and validate the JWT
        try {
            $jwt = $this->validationService->validateIdToken($idToken);
        } catch (Exception $ex) {
            throw new BadCredentialsException('Invalid token');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $jwt,
            $providerKey
        );
    }

    /**
     * @param TokenInterface $token
     * @param UserProviderInterface $userProvider
     * @param                          $providerKey
     *
     * @return PreAuthenticatedToken
     *
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     */
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        // Get the user for the injected UserProvider
        $username = $this->getUsernameFromJWT($token->getCredentials());

        if (!$username) {
            throw new CustomUserMessageAuthenticationException('Username not found');
        }

        $user = $userProvider->loadUserByUsername($username);

        return new PreAuthenticatedToken(
            $user,
            $token,
            $providerKey,
            $user->getRoles()
        );
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed: {$exception->getMessage()}", 403);
    }

    private function getUsernameFromJWT(Token $jwt)
    {
        return $jwt->getClaim('sub');
    }

    private function translateScopesToRoles($jwt)
    {
        $roles = array();
        $roles[] = 'ROLE_OAUTH_AUTHENTICATED';
        if (isset($jwt->scope)) {
            $scopes = explode(' ', $jwt->scope);
            if (array_search('read:messages', $scopes) !== false) {
                $roles[] = 'ROLE_OAUTH_READER';
            }
        }
        return $roles;
    }

}