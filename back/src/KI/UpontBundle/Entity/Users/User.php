<?php

namespace KI\UpontBundle\Entity\Users;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Image de profil
     * @ORM\OneToOne(targetEntity="KI\UpontBundle\Entity\Image", cascade={"persist", "remove"})
     * @Assert\Valid()
     */
    protected $image;

    /**
     * Genre [M|Mme]
     * @ORM\Column(name="gender", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $gender;

    /**
     * Identifiant DSI
     * @ORM\Column(name="dsi", type="string", nullable=true)
     * @Assert\Type("string")
     */
    protected $dsi;

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
     * Promo (format: '0*', ie 016, 017...)
     * @ORM\Column(name="promo", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     * @Assert\Length(min=2,max=3)
     */
    protected $promo;

    /**
     * Département [1A|GCC|GCC-Archi|GMM|GI|IMI|VET|SEGF]
     * @ORM\Column(name="department", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $department;

    /**
     * Origine [CC|DD]
     * @ORM\Column(name="origin", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $origin;

    /**
     * Nationalité
     * @ORM\Column(name="nationality", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $nationality;

    /**
     * Chambre (M016, A53, 3èmeG), lieu de résidence
     * @ORM\Column(name="location", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $location;

    /**
     * Téléphone au format 06.12.34.56.78
     * @ORM\Column(name="phone", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $phone;

    /**
     * Pseudo Skype
     * @ORM\Column(name="skype", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $skype;

    /**
     * Groupes de permissions FOSUserBundle
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    /**
     * Appareils mobiles enregistrés pour recevoir des notifications Push
     * @ORM\OneToMany(targetEntity="KI\UpontBundle\Entity\Users\Device", mappedBy="owner")
     */
    protected $devices;

    /**
     * Clubs auquels l'utilisateur n'est PAS abonné
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Users\Club")
     */
    protected $clubsNotFollowed;

    /**
     * Cours que l'utilisateur suit
     * @ORM\ManyToMany(targetEntity="KI\UpontBundle\Entity\Publications\Course", inversedBy="attendees", cascade={"persist"})
     */
    protected $courses;

    /**
     * Tableau contenant les préférences utilisateurs. Les valeurs possibles des clés de ce tableau ainsi que
     * leur valeurs par défaut sont définies dans $preferencesArray
     * @ORM\Column(name="preferences", type="array", nullable=true)
     * @JMS\Expose
     * @Assert\Type("array")
     */
    protected $preferences = array();

    /**
     * Token faible permettant de créer des urls personnalisées pour l'user
     * @ORM\Column(name="token", type="string", nullable=true)
     * @Assert\Type("string")
     */
    protected $token;

    protected $preferencesArray = array(
        'notif_followed_event' => true,
        'notif_followed_news'  => true,
        //'notif_followed_poll'  => true,
        'notif_ponthub'        => true,
        //'notif_ki_answer'      => true,
        //'notif_shotgun_h-1'    => false,
        //'notif_shotgun_m-5'    => false,
        'notif_followed_annal' => true,
        //'notif_next_class'     => true,
        'notif_shotgun_freed'     => true,
        'notif_achievement'    => true,
        'notif_next_level'     => true
    );

    /**
     * @JMS\VirtualProperty()
     */
    public function imageUrl()
    {
        return $this->image !== null ? $this->image->getWebPath() : 'uploads/images/default-user.png';
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function nick()
    {
        return $this->nickname !== null ? $this->nickname : $this->acronyme();
    }

    protected function acronyme()
    {
        $r = '';
        foreach(explode(' ', $this->firstName . ' ' . $this->lastName) as $v)
            $r .= $v[0];
        return $r . '\'' . $this->promo;
    }

    // On définit des alias pour le slug
    public function getSlug() { return $this->getUsername(); }
    public function setSlug($slug) { return $this->setUsername($slug); }




    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->devices = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set dsi
     *
     * @param string $dsi
     * @return User
     */
    public function setDsi($dsi)
    {
        $this->dsi = $dsi;

        return $this;
    }

    /**
     * Get dsi
     *
     * @return string
     */
    public function getDsi()
    {
        return $this->dsi;
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
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Set promo
     *
     * @param string $promo
     * @return User
     */
    public function setPromo($promo)
    {
        $this->promo = $promo;

        return $this;
    }

    /**
     * Get promo
     *
     * @return string
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * Set department
     *
     * @param string $department
     * @return User
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set origin
     *
     * @param string $origin
     * @return User
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     * @return User
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get nationality
     *
     * @return string
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return User
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set skype
     *
     * @param string $skype
     * @return User
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;

        return $this;
    }

    /**
     * Get skype
     *
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * Set image
     *
     * @param \KI\UpontBundle\Entity\Image $image
     * @return User
     */
    public function setImage(\KI\UpontBundle\Entity\Image $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \KI\UpontBundle\Entity\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add devices
     *
     * @param \KI\UpontBundle\Entity\Device $devices
     * @return User
     */
    public function addDevice(\KI\UpontBundle\Entity\Users\Device $devices)
    {
        $this->devices[] = $devices;

        return $this;
    }

    /**
     * Remove devices
     *
     * @param \KI\UpontBundle\Entity\Device $devices
     */
    public function removeDevice(\KI\UpontBundle\Entity\Users\Device $devices)
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
     * Add courses
     *
     * @param \KI\UpontBundle\Entity\Publications\Course $course
     * @return User
     */
    public function addCourse(\KI\UpontBundle\Entity\Publications\Course $course)
    {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param \KI\UpontBundle\Entity\Publications\Course $course
     */
    public function removeCourse(\KI\UpontBundle\Entity\Publications\Course $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Add Club Not Followed
     *
     * @param \KI\UpontBundle\Entity\Club $club
     * @return User
     */
    public function addClubNotFollowed(\KI\UpontBundle\Entity\Users\Club $club)
    {
        $this->clubsNotFollowed[] = $club;

        return $this;
    }

    /**
     * Remove Club Not Followed
     *
     * @param \KI\UpontBundle\Entity\Club $club
     */
    public function removeClubNotFollowed(\KI\UpontBundle\Entity\Users\Club $club)
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
     * Get Preference $key value
     *
     * @param string $key
     * @return string
     */
    public function getPreference($key)
    {
        if(array_key_exists($key, $this->preferences))
            return $this->preferences[$key];
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
}
