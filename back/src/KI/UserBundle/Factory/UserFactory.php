<?php

namespace KI\UserBundle\Factory;

use FOS\UserBundle\Model\UserManagerInterface;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\UserRegistrationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserFactory
{
    private $userManager;
    private $eventDispatcher;

    public function __construct(UserManagerInterface $userManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->userManager = $userManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    private function array_value($array, $key, $default_value = null)
    {
        if (!is_array($array))
            return null;

        return array_key_exists($key, $array) ? $array[$key] : $default_value;
    }

    /**
     * Creates a new user for the given username
     *
     * @param string $username The username
     * @param array $roles Roles assigned to user
     * @param array $attributes Attributes provided by SSO server
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function createUser($username, array $roles, array $attributes)
    {
        $email = $this->array_value($attributes, 'email');

        /**
         * @var $user User
         */
        $user = $this->userManager->createUser();

        $user->setUsername($username);
        $user->setEmail($email);

        $loginMethod = $this->array_value($attributes, 'loginMethod');

        if($loginMethod == 'form')
            $password = $this->array_value($attributes, 'password');
        else {
            $password = substr(str_shuffle(strtolower(sha1(rand() . time() . 'salt'))), 0, 8);
            $attributes['password'] = $password;
            $loginMethod = 'form';
        }

        $user->setLoginMethod($loginMethod);
        $user->setPlainPassword($password);

        $user->setFirstName($this->array_value($attributes, 'firstName'));
        $user->setLastName($this->array_value($attributes, 'lastName'));
        $user->setPromo($this->array_value($attributes, 'promo'));
        $user->setOrigin($this->array_value($attributes, 'origin'));
        $user->setDepartment($this->array_value($attributes, 'department'));

        $user->setEnabled(true);

        $this->userManager->updateUser($user);

        $userRegistration = new UserRegistrationEvent($user, $attributes);
        $this->eventDispatcher->dispatch('upont.user_registration', $userRegistration);

        return $user;
    }
}
