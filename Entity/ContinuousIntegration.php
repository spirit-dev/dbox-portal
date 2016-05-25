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
 * File           ContinuousIntegration.php
 * Updated the    25/05/16 13:54
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContinuousIntegration
 *
 * @ORM\Table(name="continuous_integration")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class ContinuousIntegration {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var object
     *
     * @ORM\ManyToOne(targetEntity="SpiritDev\Bundle\DBoxPortalBundle\Entity\Project", inversedBy="id")
     * @ORM\JoinColumn(name="project", referencedColumnName="id")
     */
    protected $project;

    /**
     * @var string
     *
     * @ORM\Column(name="ci_name", type="string")
     */
    protected $ciName;

    /**
     * @var string
     *
     * @ORM\Column(name="access_url", type="string", nullable=true)
     */
    protected $accessUrl;

    /**
     * @var object
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="parametrized", type="boolean")
     */
    protected $parametrized;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="array")
     */
    protected $parameters;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="for_developement", type="boolean", nullable=true)
     */
    protected $forDevelopment;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        // Creating creation date
        $this->createdAt = new \DateTime();
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
     * @return object
     */
    public function getProject() {
        return $this->project;
    }

    /**
     * @param object $project
     */
    public function setProject($project) {
        $this->project = $project;
    }

    /**
     * @return string
     */
    public function getCiName() {
        return $this->ciName;
    }

    /**
     * @param string $ciName
     */
    public function setCiName($ciName) {
        $this->ciName = $ciName;
    }

    /**
     * @return string
     */
    public function getAccessUrl() {
        return $this->accessUrl;
    }

    /**
     * @param string $accessUrl
     */
    public function setAccessUrl($accessUrl) {
        $this->accessUrl = $accessUrl;
    }

    /**
     * @return object
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param object $createdAt
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return boolean
     */
    public function isParametrized() {
        return $this->parametrized;
    }

    /**
     * @param boolean $parametrized
     */
    public function setParametrized($parametrized) {
        $this->parametrized = $parametrized;
    }

    /**
     * @return array
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
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
     * @return boolean
     */
    public function isForDevelopment() {
        return $this->forDevelopment;
    }

    /**
     * @param boolean $forDevelopment
     */
    public function setForDevelopment($forDevelopment) {
        $this->forDevelopment = $forDevelopment;
    }
}