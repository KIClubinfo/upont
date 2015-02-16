<?php

namespace KI\UpontBundle\Entity\Ponthub;

use KI\UpontBundle\Entity\Ponthub\PonthubFile;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ExclusionPolicy("all")
 */
class Game extends PonthubFile
{
    /**
     * Année
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @Expose
     * @Assert\Range(min = 1000, max = 2050)
     */
    protected $year;

    /**
     * Studio/développeur
     * @ORM\Column(name="studio", type="string", nullable=true)
     * @Expose
     * @Assert\Type("string")
     */
    protected $studio;
    
    
    
    
    
    
    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Set year
     *
     * @param integer $year
     * @return Game
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set studio
     *
     * @param string $studio
     * @return Game
     */
    public function setStudio($studio)
    {
        $this->studio = $studio;

        return $this;
    }

    /**
     * Get studio
     *
     * @return string 
     */
    public function getStudio()
    {
        return $this->studio;
    }
}
