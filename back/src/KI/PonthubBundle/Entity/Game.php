<?php

namespace KI\PonthubBundle\Entity;

use KI\PonthubBundle\Entity\PonthubFile;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Game extends PonthubFile
{
    /**
     * Année
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @JMS\Expose
     * @Assert\Range(min = 1000, max = 2050)
     */
    protected $year;

    /**
     * Studio/développeur
     * @ORM\Column(name="studio", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $studio;

    /**
     * Operating System
     * @ORM\Column(name="os", type="string", nullable=true)
     * @JMS\Expose
     * @Assert\Type("string")
     */
    protected $os;






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

    /**
     * Add listTag
     *
     * @param \KI\CoreBundle\Entity\Tag $listTag
     *
     * @return Game
     */
    public function addListTag(\KI\CoreBundle\Entity\Tag $listTag)
    {
        $this->listTags[] = $listTag;

        return $this;
    }

    /**
     * Remove listTag
     *
     * @param \KI\CoreBundle\Entity\Tag $listTag
     */
    public function removeListTag(\KI\CoreBundle\Entity\Tag $listTag)
    {
        $this->listTags->removeElement($listTag);
    }

    /**
     * Get listTags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListTags()
    {
        return $this->listTags;
    }

    /**
     * Add listGenre
     *
     * @param \KI\PonthubBundle\Entity\Genre $listGenre
     *
     * @return Game
     */
    public function addListGenre(\KI\PonthubBundle\Entity\Genre $listGenre)
    {
        $this->listGenres[] = $listGenre;

        return $this;
    }

    /**
     * Remove listGenre
     *
     * @param \KI\PonthubBundle\Entity\Genre $listGenre
     */
    public function removeListGenre(\KI\PonthubBundle\Entity\Genre $listGenre)
    {
        $this->listGenres->removeElement($listGenre);
    }

    /**
     * Get listGenres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListGenres()
    {
        return $this->listGenres;
    }

    /**
     * Add listLike
     *
     * @param \KI\UserBundle\Entity\User $listLike
     *
     * @return Game
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
     * @return Game
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
     * @return Game
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

    /**
     * Set os
     *
     * @param string $os
     *
     * @return Game
     */
    public function setOs($os)
    {
        $this->os = $os;

        return $this;
    }

    /**
     * Get os
     *
     * @return string
     */
    public function getOs()
    {
        return $this->os;
    }
}
