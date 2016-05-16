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
 * File           GitLabAPI.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GitLabAPI
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
class GitLabAPI extends GitLabAPICore implements GitLabAPICoreInterface {

    /**
     * GitLabAPI constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
        $this->serverAvailable = $this->isAvailable();
    }

    /**
     * Check if server is available
     * @return mixed
     */
    public function isAvailable() {
        try {
            $user = $this->gitLabClient->api($this::API_USERS)->show(1);
            if ($user == null) {
                $this->serverAvailable = null;
            } else {
                $this->serverAvailable = true;
            }
        } catch (\Exception $e) {
            $this->serverAvailable = false;
        }
        return $this->serverAvailable;
    }

    /**
     * @return mixed
     */
    public function listProjects() {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_PROJECTS)->all();
    }

    /**
     * @param Project $project
     * @return array|\Gitlab\Model\Project|null
     */
    public function createProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Creating project
        $gitLabProject = \Gitlab\Model\Project::create($this->gitLabClient, $project->getName(), array(
            'description' => $project->getDescription(),
            'issue_enabled' => $project->isGitLabIssueEnabled(),
            'wiki_enabled' => $project->isGitLabWikiEnabled(),
            'snippets_enabled' => $project->isGitLabSnippetsEnabled(),
            'builds_enabled' => false
        ));
        // returning created object
        return $gitLabProject;
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getProject($projectId) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_PROJECTS)->show($projectId);
    }

    /**
     * Count nb commits per branches and per projects
     * @param Project $project
     * @return mixed
     */
    public function defineGitCommits(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        // Prepare vars
        $gitNbCommit = array();
        $i = 0;

        // Get project branches
        $gitBranches = $this->getProjectBranches($project->getGitLabProjectId());

        // Loop on branches
        foreach ($gitBranches as $branch) {
            // Setting datas
            $gitNbCommit[$i]['name'] = $branch['name'];
            $gitNbCommit[$i]['nb_commits'] = count($this->getProjectCommits($project->getGitLabProjectId(), $branch['name']));
            $gitNbCommit[$i]['protected'] = $branch['protected'];
            $i++;
        }

        return $gitNbCommit;
    }

    /**
     * Retrieve Gitlab Project branches
     * @param $projectId
     * @return mixed
     */
    public function getProjectBranches($projectId) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_REPOSITORIES)->branches($projectId);
    }

    /**
     * Retrieve gitlab Commits
     * @param $projectId
     * @param $branchName
     * @return mixed
     */
    public function getProjectCommits($projectId, $branchName) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_REPOSITORIES)->commits($projectId, 0, null, $branchName);
    }

    /**
     * Retrieve gitlab Tags
     * @param $projectId
     * @return mixed
     */
    public function getProjectTags($projectId) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_REPOSITORIES)->tags($projectId);
    }

    /**
     * @param $projectId
     * @param $dbUsers
     * @return mixed
     */
    public function setTeamMembers($projectId, $dbUsers) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Getting all users
        $gitLabUsers = $this->getAllUsers();
        // Double loop on db and gitlab users to retrieve gitlab users ids
        foreach ($dbUsers as $dbUser) {
            foreach ($gitLabUsers as $gitLabUser) {
                if ($dbUser->getUsername() == $gitLabUser['username']) {
                    // add gitlab user to gitlab project team mate
                    $this->gitLabClient->api($this::API_PROJECTS)->addMember($projectId, $gitLabUser['id'], 30);
                }
            }
        }
        return $this->getProjectMembers($projectId);
    }

    /**
     * @return mixed
     */
    public function getAllUsers() {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_USERS)->all();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function getProjectMembers($projectId) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_PROJECTS)->members($projectId);
    }

    /**
     * @param $projectId
     * @param User $user
     * @return mixed
     */
    public function addTeamMember($projectId, User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        $this->gitLabClient->api($this::API_PROJECTS)->addMember($projectId, $user->getGitLabId(), 30);
        return $this->getProjectMembers($projectId);
    }

    /**
     * @param $projectId
     * @param User $user
     * @return mixed
     */
    public function delTeamMember($projectId, User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        $this->gitLabClient->api($this::API_PROJECTS)->removeMember($projectId, $user->getGitLabId());
        return $this->getProjectMembers($projectId);
    }

    /**
     * @param array $dBUsers
     * @param $autocreate
     * @return mixed
     */
    public function diffUsers(array $dBUsers, $autocreate) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Get GitLab Users
        $gitLabUsers = $this->getAllUsers();
        // Get Diff list via array comparison
        $undefinedUser = $this->getDiffUsers($dBUsers, $gitLabUsers);

        // If some users are not created, create them
        if ($autocreate && count($undefinedUser) > 0) {
            foreach ($undefinedUser as $userToCreate) {
                $this->createUser($userToCreate);
            }
            // update gitlab user list
            $gitLabUsers = $this->gitLabClient->api($this::API_USERS)->all();
        }

        // return list or updated list
        return $gitLabUsers;
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Create user
        $newUser = $this->gitLabClient->api($this::API_USERS)->create(
            $user->getEmail(),
            '1234567890',
            array(
                'username' => $user->getUsername(),
                'name' => $user->getCommonName(),
                'provider' => $this->ldapProvider,
                'extern_uid' => $user->getDn(),
                'confirm' => false,
                'projects_limit' => 0
            )
        );
        // Return created user
        return $newUser;
    }

    /**
     * Retrieve user
     * @param User $user
     * @return mixed
     */
    public function getUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        return $this->gitLabClient->api($this::API_USERS)->show($user->getGitLabId());
    }

    /**
     * Delete Gitlab user
     * @param User $user
     * @return mixed
     */
    public function deleteUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $this->gitLabClient->api($this::API_USERS)->remove($user->getGitLabId());
        return true;
    }

    /**
     * Lock user account
     * @param User $user
     * @return mixed
     */
    public function lockUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        return $this->gitLabClient->api($this::API_USERS)->block($user->getGitLabId());
    }

    /**
     * Unlock user account
     * @param User $user
     * @return mixed
     */
    public function unlockUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        return $this->gitLabClient->api($this::API_USERS)->unblock($user->getGitLabId());
    }

    /**
     * Create WebHook on a defined project
     * @param Project $project
     * @param $url
     * @return mixed
     */
    public function setProjectWebHook(Project $project, $url) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Create Hook
        $webHook = $this->gitLabClient->api($this::API_PROJECTS)->addHook($project->getGitLabProjectId(), $url);
        // Return created Hook
        return $webHook;
    }

    /**
     * Adding a file to a project
     * @param Project $project
     * @param $fileName
     * @param $fileContent
     * @param $commitMessage
     * @param $branch
     * @param string $encoding
     * @return null
     */
    public function addFile(Project $project, $fileName, $fileContent, $commitMessage, $branch = 'master', $encoding = 'text') {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Create file
        $file = $this->gitLabClient->api($this::API_REPOSITORIES)->createFile($project->getGitLabProjectId(), $fileName, $fileContent, $branch, $commitMessage, $encoding);
        // Return created file
        return $file;
    }

    /**
     * Getting files of a project
     * @param Project $project
     * @return mixed
     */
    public function getTree(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Create file
        $tree = $this->gitLabClient->api($this::API_REPOSITORIES)->tree($project->getGitLabProjectId());
        // Return created file
        return $tree;
    }

    /**
     * @param Project $project
     * @param $filePath
     * @param string $ref
     * @return mixed
     */
    public function getFile(Project $project, $filePath, $ref = 'master') {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Create file
        $file = $this->gitLabClient->api($this::API_REPOSITORIES)->getFile($project->getGitLabProjectId(), $filePath, $ref);
        // Return created file
        return $file;
    }

    /**
     * Deletes a project
     * @param $projectId
     * @return mixed
     */
    public function deleteProject($projectId) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }
        // Delete project
        $project = $this->gitLabClient->api($this::API_PROJECTS)->remove($projectId);
        // Return delete project
        return $project;
    }
}