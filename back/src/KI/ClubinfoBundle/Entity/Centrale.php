<?php

namespace KI\ClubinfoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use KI\CoreBundle\Entity\Likeable;
/**
 * Centrale
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Centrale extends Likeable
{
    /**
     * @var string
     *
     * @ORM\Column(name="product", type="string", length=255)
     */
    private $product;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="integer")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="integer")
     */
    private $endDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;



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
     * @param integer $startDate
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
     * @return integer
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param integer $endDate
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
     * @return integer
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set status
     *
     * @param integer $status
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
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add listLike
     *
     * @param \KI\UserBundle\Entity\User $listLike
     *
     * @return Centrale
     */
    public function addListLike(\KI\UserBundle\Entity\User $listLike)
    {
        $this->listLikes[] = $listLike;

        return $this;
    }

    /**
     * Remove listLike
     *
     * @param \KI\UserBundle\Entity\User $listLike
     */
    public function removeListLike(\KI\UserBundle\Entity\User $listLike)
    {
        $this->listLikes->removeElement($listLike);
    }

    /**
     * Get listLikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListLikes()
    {
        return $this->listLikes;
    }

    /**
     * Add listDislike
     *
     * @param \KI\UserBundle\Entity\User $listDislike
     *
     * @return Centrale
     */
    public function addListDislike(\KI\UserBundle\Entity\User $listDislike)
    {
        $this->listDislikes[] = $listDislike;

        return $this;
    }

    /**
     * Remove listDislike
     *
     * @param \KI\UserBundle\Entity\User $listDislike
     */
    public function removeListDislike(\KI\UserBundle\Entity\User $listDislike)
    {
        $this->listDislikes->removeElement($listDislike);
    }

    /**
     * Get listDislikes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListDislikes()
    {
        return $this->listDislikes;
    }

    /**
     * Add listComment
     *
     * @param \KI\CoreBundle\Entity\Comment $listComment
     *
     * @return Centrale
     */
    public function addListComment(\KI\CoreBundle\Entity\Comment $listComment)
    {
        $this->listComments[] = $listComment;

        return $this;
    }

    /**
     * Remove listComment
     *
     * @param \KI\CoreBundle\Entity\Comment $listComment
     */
    public function removeListComment(\KI\CoreBundle\Entity\Comment $listComment)
    {
        $this->listComments->removeElement($listComment);
    }

    /**
     * Get listComments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListComments()
    {
        return $this->listComments;
    }
}
