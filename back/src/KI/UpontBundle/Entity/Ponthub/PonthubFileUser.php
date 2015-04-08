<?php

namespace KI\UpontBundle\Entity\Ponthub;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class PonthubFileUser
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Ponthub\PonthubFile")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    protected $file;

    /**
     * @ORM\ManyToOne(targetEntity="KI\UpontBundle\Entity\Users\User", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    protected $user;

    /**
     * Date du shotgun (timestamp)
     * @ORM\Column(name="date", type="integer")
     * @JMS\Expose
     * @Assert\Type("integer")
     */
    protected $date;

    //===== GENERATED AUTOMATICALLY =====//mm

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
     * Set file
     *
     * @param \KI\UpontBundle\Entity\Ponthub\PonthubFile $file
     * @return PonthubFileUser
     */
    public function setFile(\KI\UpontBundle\Entity\Ponthub\PonthubFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \KI\UpontBundle\Entity\Ponthub\PonthubFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set user
     *
     * @param \KI\UpontBundle\Entity\User $user
     * @return EventUser
     */
    public function setUser(\KI\UpontBundle\Entity\Users\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \KI\UpontBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return EventUser
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }
}
