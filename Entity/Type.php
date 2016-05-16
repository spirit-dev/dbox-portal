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
 * File           Type.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity
 */
class Type {
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="canonical_name", type="string", length=255, nullable=false)
     */
    private $canonicalName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand", mappedBy="type")
     */
    private $demands;

    /**
     * Constructor
     */
    public function __construct() {
        $this->demands = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Type
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get canonicalName
     *
     * @return string
     */
    public function getCanonicalName() {
        return $this->canonicalName;
    }

    /**
     * Set canonicalName
     *
     * @param string $canonicalName
     *
     * @return Type
     */
    public function setCanonicalName($canonicalName) {
        $this->canonicalName = $canonicalName;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Type
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Add demand
     *
     * @param Demand $demand
     *
     * @return Demand
     */
    public function addDemand(Demand $demand) {
        $this->demands[] = $demand;

        return $this;
    }

    /**
     * Remove demand
     *
     * @param Demand $demand
     */
    public function removeDemand(Demand $demand) {
        $this->demands->removeElement($demand);
    }

    /**
     * Get demands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDemands() {
        return $this->demands;
    }
}
