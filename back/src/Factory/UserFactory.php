<?php

namespace App\Factory;

use FOS\UserBundle\Model\UserManagerInterface;
use App\Entity\User;
use App\Event\UserRegistrationEvent;
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

        $password = $this->array_value($attributes, 'password');
        if(!$password) {
            $password = substr(str_shuffle(strtolower(sha1(rand() . time() . 'salt'))), 0, 8);
            $attributes['password'] = $password;
        }

        $user->setLoginMethod($this->array_value($attributes, 'loginMethod', 'form'));
        $user->setPlainPassword($password);

        $user->setFirstName($this->array_value($attributes, 'firstName'));
        $user->setLastName($this->array_value($attributes, 'lastName'));
        $user->setPromo($this->array_value($attributes, 'promo'));
        $user->setOrigin($this->array_value($attributes, 'origin'));
        $user->setDepartment($this->array_value($attributes, 'department'));

        $user->setEnabled(true);

        $this->userManager->updateUser($user);

        $this->eventDispatcher->dispatch(new UserRegistrationEvent($user, $attributes));

        return $user;
    }
}
