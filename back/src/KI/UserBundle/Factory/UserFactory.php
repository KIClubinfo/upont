<?php

namespace KI\UserBundle\Factory;

use FOS\UserBundle\Model\UserManagerInterface;
use KI\UserBundle\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;

class UserFactory
{

    private $userManager;
    protected $swiftMailer;
    protected $twigEngine;

    public function __construct(
        UserManagerInterface $userManager
//        Swift_Mailer $swiftMailer,
//        TwigEngine $twigEngine
    )
    {
        $this->userManager = $userManager;
//        $this->swiftMailer = $swiftMailer;
//        $this->twigEngine = $twigEngine;
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

        $hasPassword = $this->array_value($attributes, 'loginMethod') == 'form';

        //FIXME: hackhackhack
        if($hasPassword)
            $password = $this->array_value($attributes, 'password');
        else
            $password = 'sso-cas-enpc';


        $user->setLoginMethod($hasPassword);
        $user->setPlainPassword($password);

        $user->setFirstName($this->array_value($attributes, 'firstName'));
        $user->setLastName($this->array_value($attributes, 'lastName'));
        $user->setPromo($this->array_value($attributes, 'promo'));
        $user->setOrigin($this->array_value($attributes, 'origin'));
        $user->setDepartment($this->array_value($attributes, 'department'));

        $user->setEnabled(true);

        $this->userManager->updateUser($user);

        // Envoi du mail
        $message = Swift_Message::newInstance()
            ->setSubject('Inscription uPont')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo($email)
            ->setBody($this->twigEngine->render('KIUserBundle::registration.txt.twig', $attributes), 'text/html');

        $this->swiftMailer->send($message);
    }
}
