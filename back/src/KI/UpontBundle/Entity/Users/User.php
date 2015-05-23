<?php

namespace KI\UpontBundle\Entity\Users;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * La classe User est divisée en deux (autre partie dans CoreUser)
 * Ici sont stockées les infos secondaires (infos de contact) dont on n'a pas
 * besoin 100% du temps.
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User extends \KI\UpontBundle\Entity\Users\CoreUser
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
     * Autorisation de rendre publiques les stats Foyer
     * @ORM\Column(name="statsFoyer", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $statsFoyer;

    /**
     * Autorisation de rendre publiques les stats PontHub
     * @ORM\Column(name="statsPonthub", type="boolean", nullable=true)
     * @JMS\Expose
     * @Assert\Type("boolean")
     */
    protected $statsPonthub;

    /**
     * Acronyme
     * @ORM\Column(name="acronyme", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $acronyme;

    protected function acronyme()
    {
        $r = '';
        foreach (explode(' ', $this->firstName.' '.$this->lastName) as $v)
            $r .= $v[0];
        return $r.'\''.$this->promo;
    }

    public function setAcronyme($acronyme = '')
    {
        $this->acronyme = ($acronyme == '') ? $this->acronyme() : $acronyme;
        return $this;
    }

    public function getAcronyme() { return ($this->acronyme == '') ? $this->acronyme() : $this->acronyme; }



    //===== GENERATED AUTOMATICALLY =====//

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
     * Set statsFoyer
     *
     * @param string $statsFoyer
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
     * @param string $statsPonthub
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
}
