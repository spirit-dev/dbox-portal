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
 * File           Project.php
 * Updated the    24/05/16 15:49
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="SpiritDev\Bundle\DBoxPortalBundle\Entity\ProjectRepository")
 * @Vich\Uploadable
 */
class Project {

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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="canonical_name", type="string", nullable=true)
     */
    private $canonicalName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var boolean
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="SpiritDev\Bundle\DBoxUserBundle\Entity\User", inversedBy="username")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var object
     *
     * @ORM\ManyToMany(targetEntity="SpiritDev\Bundle\DBoxUserBundle\Entity\User")
     * ORM\JoinTable(name="project_members",
     *      joinColumns={ORM\JoinColumn(name="member_id", referencedColumnName="id")},
     *      inverseJoinColumns={ORM\JoinColumn(name="project_id", referencedColumnName="id")}
     * )
     */
    private $teamMembers;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="project_image", fileNameProperty="imageName")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $imageName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="git_lab_issue_enabled", type="boolean", nullable=true)
     */
    private $gitLabIssueEnabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="git_lab_wiki_enabled", type="boolean", nullable=true)
     */
    private $gitLabWikiEnabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="git_lab_snippets_enabled", type="boolean", nullable=true)
     */
    private $gitLabSnippetsEnabled;

    /**
     * @var integer
     * @ORM\Column(name="git_lab_project_id", type="integer", nullable=true)
     */
    private $gitLabProjectId;

    /**
     * @var string
     * @ORM\Column(name="git_lab_ssh_url_to_repo", type="string", nullable=true)
     */
    private $gitLabSshUrlToRepo;

    /**
     * @var string
     * @ORM\Column(name="git_lab_http_url_to_repo", type="string", nullable=true)
     */
    private $gitLabHttpUrlToRepo;

    /**
     * @var string
     * @ORM\Column(name="git_lab_web_url", type="string", nullable=true)
     */
    private $gitLabWebUrl;

    /**
     * @var string
     * @ORM\Column(name="git_lab_namespace", type="string", nullable=true)
     */
    private $gitLabNamespace;

    /**
     * @var integer
     * @ORM\Column(name="git_nb_commits", type="integer", nullable=true)
     */
    private $gitNbCommits;

    /**
     * @var \DateTime
     * @ORM\Column(name="git_commit_last_update", type="datetime", nullable=true)
     */
    private $gitCommitLastUpdate;

    /**
     * @var integer
     * @ORM\Column(name="redmine_project_id", type="integer", nullable=true)
     */
    private $redmineProjectId;

    /**
     * @var string
     * @ORM\Column(name="redmine_project_identifier", type="string", nullable=true)
     */
    private $redmineProjectIdentifier;

    /**
     * @var string
     * @ORM\Column(name="redmine_web_url", type="string", nullable=true)
     */
    private $redmineWebUrl;

    /**
     * @var string
     * @ORM\Column(name="language_type", type="string", nullable=true)
     */
    private $languageType;

    /**
     * @var integer
     * @ORM\Column(name="sonar_project_id", type="integer", nullable=true)
     */
    private $sonarProjectId;

    /**
     * @var string
     * @ORM\Column(name="sonar_project_key", type="string", nullable=true)
     */
    private $sonarProjectKey;

    /**
     * @var string
     * @ORM\Column(name="sonar_project_url", type="string", nullable=true)
     */
    private $sonarProjectUrl;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $vcsManaged;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $pmManaged;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $ciDevManaged;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $qaDevManaged;

    /**
     * @var boolean
     * @ORM\Column(name="security_assessments", type="boolean", nullable=true)
     */
    private $securityAssessments;

    /**
     * Constructor
     */
    public function __construct() {
        $this->teamMembers = new ArrayCollection();
        $this->active = false;
        $this->gitNbCommits = 0;
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist() {
        // Creating creation date
        $this->createdAt = new \DateTime();
        // Defining common name
        $vowels = array(" ", "-", "_", ".", ",");
        $this->canonicalName = strtolower(str_replace($vowels, "", $this->getName()));
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate() {
        // Set update date
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function __toString() {
        return $this->getName();
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
    public function getCanonicalName() {
        return $this->canonicalName;
    }

    /**
     * @param string $canonicalName
     */
    public function setCanonicalName($canonicalName) {
        $this->canonicalName = $canonicalName;
    }

    /**
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * @param int $owner
     */
    public function setOwner($owner) {
        $this->owner = $owner;
    }

    /**
     * @return object
     */
    public function getTeamMembers() {
        return $this->teamMembers;
    }

    /**
     * @param object $teamMembers
     */
    public function setTeamMembers($teamMembers) {
        $this->teamMembers = $teamMembers;
    }

    /**
     * @param $teamMember
     */
    public function addTeamMember($teamMember) {
        $this->teamMembers[] = $teamMember;
    }

    /**
     * @param $teamMember
     */
    public function removeTeamMember($teamMember) {
        $this->teamMembers->removeElement($teamMember);
    }

    /**
     * @return boolean
     */
    public function isGitLabIssueEnabled() {
        return $this->gitLabIssueEnabled;
    }

    /**
     * @param boolean $gitLabIssueEnabled
     */
    public function setGitLabIssueEnabled($gitLabIssueEnabled) {
        $this->gitLabIssueEnabled = $gitLabIssueEnabled;
    }

    /**
     * @return boolean
     */
    public function isGitLabWikiEnabled() {
        return $this->gitLabWikiEnabled;
    }

    /**
     * @param boolean $gitLabWikiEnabled
     */
    public function setGitLabWikiEnabled($gitLabWikiEnabled) {
        $this->gitLabWikiEnabled = $gitLabWikiEnabled;
    }

    /**
     * @return boolean
     */
    public function isGitLabSnippetsEnabled() {
        return $this->gitLabSnippetsEnabled;
    }

    /**
     * @param boolean $gitLabSnippetsEnabled
     */
    public function setGitLabSnippetsEnabled($gitLabSnippetsEnabled) {
        $this->gitLabSnippetsEnabled = $gitLabSnippetsEnabled;
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
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
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
     * @return int
     */
    public function getGitLabProjectId() {
        return $this->gitLabProjectId;
    }

    /**
     * @param int $gitLabProjectId
     */
    public function setGitLabProjectId($gitLabProjectId) {
        $this->gitLabProjectId = $gitLabProjectId;
    }

    /**
     * @return string
     */
    public function getGitLabSshUrlToRepo() {
        return $this->gitLabSshUrlToRepo;
    }

    /**
     * @param string $gitLabSshUrlToRepo
     */
    public function setGitLabSshUrlToRepo($gitLabSshUrlToRepo) {
        $this->gitLabSshUrlToRepo = $gitLabSshUrlToRepo;
    }

    /**
     * @return string
     */
    public function getGitLabHttpUrlToRepo() {
        return $this->gitLabHttpUrlToRepo;
    }

    /**
     * @param string $gitLabHttpUrlToRepo
     */
    public function setGitLabHttpUrlToRepo($gitLabHttpUrlToRepo) {
        $this->gitLabHttpUrlToRepo = $gitLabHttpUrlToRepo;
    }

    /**
     * @return string
     */
    public function getGitLabWebUrl() {
        return $this->gitLabWebUrl;
    }

    /**
     * @param string $gitLabWebUrl
     */
    public function setGitLabWebUrl($gitLabWebUrl) {
        $this->gitLabWebUrl = $gitLabWebUrl;
    }

    /**
     * @return File
     */
    public function getImageFile() {
        return $this->imageFile;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Project
     */
    public function setImageFile(File $image = null) {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImageName() {
        return $this->imageName;
    }

    /**
     * @param string $imageName
     */
    public function setImageName($imageName) {
        $this->imageName = $imageName;
    }

    /**
     * @return int
     */
    public function getGitNbCommits() {
        return $this->gitNbCommits;
    }

    /**
     * @param int $gitNbCommits
     */
    public function setGitNbCommits($gitNbCommits) {
        $this->gitNbCommits = $gitNbCommits;
    }

    /**
     * @return \DateTime
     */
    public function getGitCommitLastUpdate() {
        return $this->gitCommitLastUpdate;
    }

    /**
     * @param \DateTime $gitCommitLastUpdate
     */
    public function setGitCommitLastUpdate($gitCommitLastUpdate) {
        $this->gitCommitLastUpdate = $gitCommitLastUpdate;
    }

    /**
     * @return int
     */
    public function getRedmineProjectId() {
        return $this->redmineProjectId;
    }

    /**
     * @param int $redmineProjectId
     */
    public function setRedmineProjectId($redmineProjectId) {
        $this->redmineProjectId = $redmineProjectId;
    }

    /**
     * @return string
     */
    public function getRedmineProjectIdentifier() {
        return $this->redmineProjectIdentifier;
    }

    /**
     * @param string $redmineProjectIdentifier
     */
    public function setRedmineProjectIdentifier($redmineProjectIdentifier) {
        $this->redmineProjectIdentifier = $redmineProjectIdentifier;
    }

    /**
     * @return string
     */
    public function getRedmineWebUrl() {
        return $this->redmineWebUrl;
    }

    /**
     * @param string $redmineWebUrl
     */
    public function setRedmineWebUrl($redmineWebUrl) {
        $this->redmineWebUrl = $redmineWebUrl;
    }

    /**
     * @return string
     */
    public function getLanguageType() {
        return $this->languageType;
    }

    /**
     * @param string $languageType
     */
    public function setLanguageType($languageType) {
        $this->languageType = $languageType;
    }

    /**
     * @return string
     */
    public function getGitLabNamespace() {
        return $this->gitLabNamespace;
    }

    /**
     * @param string $gitLabNamespace
     */
    public function setGitLabNamespace($gitLabNamespace) {
        $this->gitLabNamespace = $gitLabNamespace;
    }

    /**
     * @return int
     */
    public function getSonarProjectId() {
        return $this->sonarProjectId;
    }

    /**
     * @param int $sonarProjectId
     */
    public function setSonarProjectId($sonarProjectId) {
        $this->sonarProjectId = $sonarProjectId;
    }

    /**
     * @return string
     */
    public function getSonarProjectKey() {
        return $this->sonarProjectKey;
    }

    /**
     * @param string $sonarProjectKey
     */
    public function setSonarProjectKey($sonarProjectKey) {
        $this->sonarProjectKey = $sonarProjectKey;
    }

    /**
     * @return string
     */
    public function getSonarProjectUrl() {
        return $this->sonarProjectUrl;
    }

    /**
     * @param string $sonarProjectUrl
     */
    public function setSonarProjectUrl($sonarProjectUrl) {
        $this->sonarProjectUrl = $sonarProjectUrl;
    }

    /**
     * @return boolean
     */
    public function isVcsManaged() {
        return $this->vcsManaged;
    }

    /**
     * @param boolean $vcsManaged
     */
    public function setVcsManaged($vcsManaged) {
        $this->vcsManaged = $vcsManaged;
    }

    /**
     * @return boolean
     */
    public function isPmManaged() {
        return $this->pmManaged;
    }

    /**
     * @param boolean $pmManaged
     */
    public function setPmManaged($pmManaged) {
        $this->pmManaged = $pmManaged;
    }

    /**
     * @return boolean
     */
    public function isCiDevManaged() {
        return $this->ciDevManaged;
    }

    /**
     * @param boolean $ciDevManaged
     */
    public function setCiDevManaged($ciDevManaged) {
        $this->ciDevManaged = $ciDevManaged;
    }

    /**
     * @return boolean
     */
    public function isQaDevManaged() {
        return $this->qaDevManaged;
    }

    /**
     * @param boolean $qaDevManaged
     */
    public function setQaDevManaged($qaDevManaged) {
        $this->qaDevManaged = $qaDevManaged;
    }

    /**
     * @return boolean
     */
    public function hasSecurityAssessments() {
        return $this->securityAssessments;
    }

    /**
     * @param boolean $securityAssessments
     */
    public function setSecurityAssessments($securityAssessments) {
        $this->securityAssessments = $securityAssessments;
    }
}