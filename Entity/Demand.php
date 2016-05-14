<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Demand
 *
 * @ORM\Table(name="demand", indexes={@ORM\Index(columns={"status"}), @ORM\Index(columns={"type"}), @ORM\Index(columns={"applicant"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Demand {
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
     * @ORM\Column(name="askdate", type="datetime", nullable=true)
     */
    private $askdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedate", type="datetime", nullable=true)
     */
    private $updatedate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="resolutiondate", type="datetime", nullable=true)
     */
    private $resolutiondate;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="SpiritDev\Bundle\DBoxPortalBundle\Entity\Type")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="SpiritDev\Bundle\DBoxPortalBundle\Entity\Status")
     * @ORM\JoinColumn(name="status", referencedColumnName="id")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="SpiritDev\Bundle\DBoxUserBundle\Entity\User", inversedBy="username")
     * @ORM\JoinColumn(name="applicant", referencedColumnName="id")
     */
    private $applicant;

    /**
     * @var object
     *
     * @ORM\ManyToMany(targetEntity="SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment")
     * ORM\JoinTable(name="comments",
     *      joinColumns={ORM\JoinColumn(name="comment_id", referencedColumnName="id")},
     *      inverseJoinColumns={ORM\JoinColumn(name="demand_id", referencedColumnName="id")}
     * )
     */
    private $comments;

    /**
     * @var array
     *
     * @ORM\Column(name="content", type="array")
     */
    private $content;

    /**
     * Constructor
     */
    public function __construct() {
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        // Creating creation date
        $this->askdate = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate() {
        // Set update date
        $this->updatedate = new \DateTime();
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
     * Set id
     *
     * @param $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Get askdate
     *
     * @return \DateTime
     */
    public function getAskdate() {
        return $this->askdate;
    }

    /**
     * Set askdate
     *
     * @param \DateTime $askdate
     *
     * @return Demand
     */
    public function setAskdate($askdate) {
        $this->askdate = $askdate;

        return $this;
    }

    /**
     * Get updatedate
     *
     * @return \DateTime
     */
    public function getUpdatedate() {
        return $this->updatedate;
    }

    /**
     * Set updatedate
     *
     * @param \DateTime $updatedate
     *
     * @return Demand
     */
    public function setUpdatedate($updatedate) {
        $this->updatedate = $updatedate;

        return $this;
    }

    /**
     * Get resolutiondate
     *
     * @return \DateTime
     */
    public function getResolutiondate() {
        return $this->resolutiondate;
    }

    /**
     * Set resolutiondate
     *
     * @param \DateTime $resolutiondate
     *
     * @return Demand
     */
    public function setResolutiondate($resolutiondate) {
        $this->resolutiondate = $resolutiondate;

        return $this;
    }

    /**
     * Get type
     *
     * @return \SpiritDev\Bundle\DBoxPortalBundle\Entity\Type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \SpiritDev\Bundle\DBoxPortalBundle\Entity\Type $type
     *
     * @return Demand
     */
    public function setType(\SpiritDev\Bundle\DBoxPortalBundle\Entity\Type $type = null) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get status
     *
     * @return \SpiritDev\Bundle\DBoxPortalBundle\Entity\Status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param \SpiritDev\Bundle\DBoxPortalBundle\Entity\Status $status
     *
     * @return Demand
     */
    public function setStatus(\SpiritDev\Bundle\DBoxPortalBundle\Entity\Status $status = null) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get applicant
     *
     * @return \SpiritDev\Bundle\DBoxUserBundle\Entity\User
     */
    public function getApplicant() {
        return $this->applicant;
    }

    /**
     * Set applicant
     *
     * @param \SpiritDev\Bundle\DBoxUserBundle\Entity\User $applicant
     *
     * @return Demand
     */
    public function setApplicant(\SpiritDev\Bundle\DBoxUserBundle\Entity\User $applicant = null) {
        $this->applicant = $applicant;

        return $this;
    }

    /**
     * Add comment
     *
     * @param \SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment $comment
     *
     * @return Demand
     */
    public function addComment(\SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment $comment) {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment $comment
     */
    public function removeComment(\SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment $comment) {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments() {
        return $this->comments;
    }

    /**
     * Get content
     *
     * @return array
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param array $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
}
