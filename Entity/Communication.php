<?php
/**
 * Copyright (c) 2016. Spirit-Dev
 * Licensed under GPLv3 GNU License - http://www.gnu.org/licenses/gpl-3.0.html
 *    _             _
 *   /_`_  ._._/___/ | _
 * . _//_//// /   /_.'/_'|/
 *    /
 *    
 * Since 2K10 until today
 *  
 * Hex            53 70 69 72 69 74 2d 44 65 76
 *  
 * By             Jean Bordat
 * Twitter        @Ji_Bay_
 * Mail           <bordat.jean@gmail.com>
 *  
 * File           Communication.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Communication
 * @package SpiritDev\Bundle\DBoxPortalBundle\Entity
 *
 * @ORM\Table(name="communication", indexes={@ORM\Index(columns={"id"})})
 * @ORM\Entity(repositoryClass="SpiritDev\Bundle\DBoxPortalBundle\Entity\CommunicationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Communication {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=true)
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="show_from_date", type="date", nullable=true)
     */
    private $showFromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="show_to_date", type="date", nullable=true)
     */
    private $showToDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(name="title", type="string", length=1024, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        // Setting creation date
        $this->creationDate = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate() {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    /**
     * @return \DateTime
     */
    public function getShowFromDate() {
        return $this->showFromDate;
    }

    /**
     * @param \DateTime $showFromDate
     */
    public function setShowFromDate($showFromDate) {
        $this->showFromDate = $showFromDate;
    }

    /**
     * @return \DateTime
     */
    public function getShowToDate() {
        return $this->showToDate;
    }

    /**
     * @param \DateTime $showToDate
     */
    public function setShowToDate($showToDate) {
        $this->showToDate = $showToDate;
    }

    /**
     * @return boolean
     */
    public function isActive() {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active) {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
}