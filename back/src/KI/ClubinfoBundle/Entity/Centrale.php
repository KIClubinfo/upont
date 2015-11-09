<?php

namespace KI\ClubinfoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Centrale extends Likeable
{
    /**
     * Texte décrivant la centrale
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Expose
     * @Assert\NotBlank()
     */
    protected $description;

    /**
     * Date de début de la centrale d'achat
     * @ORM\Column(name="start_date", type="integer", nullable=true)
     * @JMS\Expose
     */
    protected $startDate;

    /**
     * Date de fin de la centrale d'achat
     * @ORM\Column(name="end_date", type="integer", nullable=true)
     * @JMS\Expose
     */
    protected $endDate;

    /**
     * Produit acheté par la centrale
     * @ORM\Column(name="product", type="string", nullable=true)
     * @JMS\Expose
     */
    protected $product;

    /**
     * Statut (Annoncée|En cours|Commandée|Receptionné|Fermée)
     * @ORM\Column(name="status", type="string")
     * @JMS\Expose
     */
    protected $status;

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Centrale
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Set startDate
     *
     * @param string $startDate
     *
     * @return Centrale
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return string
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    /**
     * Set endDate
     *
     * @param string $endDate
     *
     * @return Centrale
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return string
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set product
     *
     * @param string $product
     *
     * @return Centrale
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Centrale
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

}
