<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * La classe User est divisée en deux (autre partie dans User)
 * Ici sont stockées les infos primaires dont on a besoin 100% du temps.
 * @JMS\ExclusionPolicy("all")
 */
class CoreUser extends \FOS\UserBundle\Model\User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Image de profil
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Prénom
     * @ORM\Column(name="firstName", type="string")
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    protected $firstName;

    /**
     * Nom
     * @ORM\Column(name="lastName", type="string")
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    protected $lastName;

    /**
     * Surnom/pseudo
     * @ORM\Column(name="nickname", type="string", nullable=true)
     */
    protected $nickname;

    /**
     * Groupes de permissions FOSUserBundle
     * @ORM\ManyToMany(targetEntity="App\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * Date de dernière connexion (timestamp)
     * @ORM\Column(name="lastConnect", type="integer", nullable=true)
     * @Assert\Type("integer")
     */
    protected $lastConnect;

    /**
     * Appareils mobiles enregistrés pour recevoir des notifications Push
     * @ORM\OneToMany(targetEntity="App\Entity\Device", mappedBy="owner")
     */
    protected $devices;

    /**
     * Clubs auquels l'utilisateur n'est PAS abonné
     * @ORM\ManyToMany(targetEntity="App\Entity\Club")
     */
    protected $clubsNotFollowed;

    /**
     * Tableau contenant les préférences utilisateurs. Les valeurs possibles des clés de ce tableau ainsi que
     * leur valeurs par défaut sont définies dans $preferencesArray
     * @ORM\Column(name="preferences", type="array", nullable=true)
     * @Assert\Type("array")
     */
    protected $preferences = [];

    /**
     * Token faible permettant de créer des urls personnalisées pour l'user
     * @ORM\Column(name="token", type="string", nullable=true)
     * @Assert\Type("string")
     */
    protected $token;

    /**
     * Méthode d'authentification
     * @ORM\Column(name="login_method", type="string")
     * @Assert\Type("string")
     */
    protected $loginMethod;

    protected $preferencesArray = [
        'notif_followed_event' => true,
        'notif_followed_news'  => true,
        'notif_news_perso'     => true,
        'notif_comments'       => true,
        'notif_shotgun_freed'  => true,
        'notif_ponthub'        => false,
        'notif_fixs'           => true,
        'notif_followed_annal' => true,
        //'notif_achievement'    => true,
        //'notif_next_level'     => true
    ];

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        if ($this->image !== null) {
            if (file_exists($this->image->getAbsolutePath()))
                return $this->image->getWebPath();
        }
        return 'uploads/others/default-user.png';
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function nick()
    {
        return $this->nickname !== null ? $this->nickname : $this->getName();
    }

    // On définit des alias pour le slug
    public function getSlug() { return $this->getUsername(); }
    public function setSlug($slug) { return $this->setUsername($slug); }
    public function getName() { return $this->getFirstName().' '.$this->getLastName(); }




    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->devices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clubsNotFollowed = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Add group
     *
     * @param \App\Entity\User $group
     * @return Comment
     */
    public function addGroupUser(\App\Entity\Group $group)
    {
        $this->addGroup($group);
        $group->addUser($this);

        return $this;
    }

    /**
     * Remove group
     *
     * @param \App\Entity\User $group
     */
    public function removeGroupUser(\App\Entity\Group $group)
    {
        $this->removeGroup($group);
        $group->removeUser($this);
    }

    /**
     * Get lastConnect
     *
     * @return integer
     */
    public function getLastConnect()
    {
        return $this->lastConnect;
    }

    /**
     * Set lastConnect
     *
     * @param integer $lastConnect
     * @return User
     */
    public function setLastConnect($lastConnect)
    {
        $this->lastConnect = $lastConnect;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set image
     *
     * @param \App\Entity\Image $image
     * @return User
     */
    public function setImage(\App\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \App\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add devices
     *
     * @param \App\Entity\Device $devices
     * @return User
     */
    public function addDevice(\App\Entity\Device $devices)
    {
        $this->devices[] = $devices;

        return $this;
    }

    /**
     * Remove devices
     *
     * @param \App\Entity\Device $devices
     */
    public function removeDevice(\App\Entity\Device $devices)
    {
        $this->devices->removeElement($devices);
    }

    /**
     * Get devices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Add Club Not Followed
     *
     * @param \App\Entity\Club $club
     * @return User
     */
    public function addClubNotFollowed(\App\Entity\Club $club)
    {
        $this->clubsNotFollowed[] = $club;

        return $this;
    }

    /**
     * Remove Club Not Followed
     *
     * @param \App\Entity\Club $club
     */
    public function removeClubNotFollowed(\App\Entity\Club $club)
    {
        $this->clubsNotFollowed->removeElement($club);
    }

    /**
     * Get Clubs Not Followed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClubsNotFollowed()
    {
        return $this->clubsNotFollowed;
    }

    /**
     * Add Preference
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function addPreference($key, $value)
    {
        if (array_key_exists($key, $this->preferencesArray)) {
            $this->preferences[$key] = $value;
            return true;
        }

        return false;
    }

    /**
     * Remove Preference
     *
     * @param string $key
     * @return bool
     */
    public function removePreference($key)
    {
        if (array_key_exists($key, $this->preferencesArray)) {
            unset($this->preferences[$key]);
            return true;
        }
        return false;
    }

    /**
     * Get Preferences Array
     *
     * @return array
     */
    public function getPreferences()
    {
        return array_merge($this->preferencesArray, $this->preferences);
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getLoginMethod()
    {
        return $this->loginMethod;
    }

    /**
     * @param string $loginMethod
     * @return User
     */
    public function setLoginMethod($loginMethod)
    {
        $this->loginMethod = $loginMethod;

        return $this;
    }


}
