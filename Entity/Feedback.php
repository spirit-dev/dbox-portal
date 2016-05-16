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
 * File           Feedback.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="feedback")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="SpiritDev\Bundle\DBoxPortalBundle\Entity\FeedbackRepository")
 */
class Feedback {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $readed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="SpiritDev\Bundle\DBoxUserBundle\Entity\User", inversedBy="username")
     * @ORM\JoinColumn(name="sender", referencedColumnName="id")
     */
    private $sender;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        // Creating creation date
        $this->createdAt = new \DateTime();
        // Setting readed to false
        $this->readed = false;
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
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @return boolean
     */
    public function isReaded() {
        return $this->readed;
    }

    /**
     * @param boolean $readed
     */
    public function setReaded($readed) {
        $this->readed = $readed;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getSender() {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender) {
        $this->sender = $sender;
    }

}