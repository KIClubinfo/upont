<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * La classe User est divisée en deux (autre partie dans CoreUser)
 * Ici sont stockées les infos secondaires (infos de contact) dont on n'a pas
 * besoin 100% du temps.
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends CoreUser
{
    /**
     * Genre [M|Mme]
     * @ORM\Column(name="gender", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $gender;

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
     * Cotisant BDE
     * @ORM\Column(name="allowedBde", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $allowedBde;

    /**
     * Cotisant BDS
     * @ORM\Column(name="allowedBds", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $allowedBds;

    /**
     * A fait le tutoriel d'intro ?
     * @ORM\Column(name="tour", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $tour;

    /**
     * Autorisation de rendre publiques les stats Foyer
     * @ORM\Column(name="statsFoyer", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $statsFoyer = true;

    /**
     * Autorisation de rendre publiques les stats PontHub
     * @ORM\Column(name="statsPonthub", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $statsPonthub = true;

    /**
     * Autorisation de rendre publiques les stats de la réponse D
     * @ORM\Column(name="statsFacegame", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $statsFacegame = true;

    /**
     * Solde Foyer
     * @ORM\Column(name="balance", type="float", nullable=true)
     * @JMS\Expose
     * @Assert\Type("float")
     */
    protected $balance = 0.0;

    /**
     * Activation des mails d'événements
     * @ORM\Column(name="mailEvent", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $mailEvent = true;

    /**
     * Activation des mails de modification d'événements
     * @ORM\Column(name="mailModification", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $mailModification = true;

    /**
     * Activation des mails de rappel de shotguns
     * @ORM\Column(name="mailShotgun", type="boolean")
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $mailShotgun = true;

    /**
     * Achievements de l'utilisateur
     * @ORM\OneToMany(targetEntity="App\Entity\AchievementUser", mappedBy="user", orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $achievements;

    /**
     * Clubs de l'utilisateur
     * @ORM\OneToMany(targetEntity="App\Entity\ClubUser", mappedBy="user", orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $clubs;

    /**
     * Téléchargements de l'utilisateur
     * @ORM\OneToMany(targetEntity="App\Entity\PonthubFileUser", mappedBy="user", orphanRemoval=true)
     * @Assert\Valid()
     */
    protected $downloads;

    /**
     * Transactions de l'utilisateur
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="user")
     * @Assert\Valid()
     */
    protected $transactions;

    /**
     * Notifications de l'utilisateur
     * @ORM\ManyToMany(targetEntity="App\Entity\Notification", mappedBy="recipients")
     * @Assert\Valid()
     */
    protected $notifications;

    /**
     * Notifications lues de l'utilisateur
     * @ORM\ManyToMany(targetEntity="App\Entity\Notification", mappedBy="reads")
     * @Assert\Valid()
     */
    protected $notificationsRead;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->achievements = new ArrayCollection();
        $this->clubs = new ArrayCollection();
        $this->downloads = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    protected function acronyme()
    {
        $r = '';
        foreach (explode(' ', $this->firstName.' '.$this->lastName) as $v) {
                    $r .= $v[0];
        }
        return $r.'\''.$this->promo;
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
     * Set allowedBde
     *
     * @param string $allowedBde
     * @return User
     */
    public function setAllowedBde($allowedBde)
    {
        $this->allowedBde = $allowedBde;

        return $this;
    }

    /**
     * Get allowedBde
     *
     * @return string
     */
    public function getAllowedBde()
    {
        return $this->allowedBde;
    }

    /**
     * Set allowedBds
     *
     * @param string $allowedBds
     * @return User
     */
    public function setAllowedBds($allowedBds)
    {
        $this->allowedBds = $allowedBds;

        return $this;
    }

    /**
     * Get allowedBds
     *
     * @return string
     */
    public function getAllowedBds()
    {
        return $this->allowedBds;
    }

    /**
     * Set tour
     *
     * @param string $tour
     * @return User
     */
    public function setTour($tour)
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get tour
     *
     * @return string
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Set statsFoyer
     *
     * @param bool $statsFoyer
     * @return User
     */
    public function setStatsFoyer($statsFoyer)
    {
        $this->statsFoyer = $statsFoyer;

        return $this;
    }

    /**
     * Get statsFoyer
     *
     * @return string
     */
    public function getStatsFoyer()
    {
        return $this->statsFoyer;
    }

    /**
     * Set statsPonthub
     *
     * @param bool $statsPonthub
     * @return User
     */
    public function setStatsPonthub($statsPonthub)
    {
        $this->statsPonthub = $statsPonthub;

        return $this;
    }

    /**
     * Get statsPonthub
     *
     * @return string
     */
    public function getStatsPonthub()
    {
        return $this->statsPonthub;
    }

    /**
     * Set statsFacegame
     *
     * @param bool $statsFacegame
     * @return User
     */
    public function setStatsFacegame($statsFacegame)
    {
        $this->statsFacegame = $statsFacegame;

        return $this;
    }

    /**
     * Get statsFacegame
     *
     * @return string
     */
    public function getStatsFacegame()
    {
        return $this->statsFacegame;
    }

    /**
     * Set balance
     *
     * @param string $balance
     * @return User
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * Get balance
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set mailEvent
     *
     * @param  boolean $mailEvent
     * @return User
     */
    public function setMailEvent($mailEvent)
    {
        $this->mailEvent = $mailEvent;

        return $this;
    }

    /**
     * Get mailEvent
     *
     * @return boolean
     */
    public function getMailEvent()
    {
        return $this->mailEvent;
    }

    /**
     * Set mailShotgun
     *
     * @param  boolean $mailShotgun
     * @return User
     */
    public function setMailShotgun($mailShotgun)
    {
        $this->mailShotgun = $mailShotgun;

        return $this;
    }

    /**
     * Get mailShotgun
     *
     * @return boolean
     */
    public function getMailShotgun()
    {
        return $this->mailShotgun;
    }

    /**
     * Set mailModification
     *
     * @param  boolean $mailModification
     * @return User
     */
    public function setMailModification($mailModification)
    {
        $this->mailModification = $mailModification;

        return $this;
    }

    /**
     * Get mailModification
     *
     * @return boolean
     */
    public function getMailModification()
    {
        return $this->mailModification;
    }

    /**
     * @return array
     */
    public function getClubs()
    {
        return $this->clubs;
    }
}
