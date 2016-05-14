<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Interface GitLabAPIInterface
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
interface GitLabAPICoreInterface {

    /**
     * Check if server is available
     * @return mixed
     */
    public function isAvailable();

    /**
     * Retrieve GitLab projects
     * @return mixed
     */
    public function listProjects();

    /**
     * Create GitLab project
     * @param Project $project
     * @return mixed
     */
    public function createProject(Project $project);

    /**
     * Retrieve GitLab project
     * @param $projectId
     * @return mixed
     */
    public function getProject($projectId);

    /**
     * Deletes a project
     * @param $projectId
     * @return mixed
     */
    public function deleteProject($projectId);

    /**
     * Retrieve GitLab project members
     * @param $projectId
     * @return mixed
     */
    public function getProjectMembers($projectId);

    /**
     * Retrieve Gitlab Project branches
     * @param $projectId
     * @return mixed
     */
    public function getProjectBranches($projectId);

    /**
     * Retrieve gitlab Commits
     * @param $projectId
     * @param $branchName
     * @return mixed
     */
    public function getProjectCommits($projectId, $branchName);

    /**
     * Count nb commits per branches and per projects
     * @param Project $project
     * @return mixed
     */
    public function defineGitCommits(Project $project);

    /**
     * Retrieve gitlab Tags
     * @param $projectId
     * @return mixed
     */
    public function getProjectTags($projectId);

    /**
     * Set GitLab project members
     * @param $projectId
     * @param $users
     * @return mixed
     */
    public function setTeamMembers($projectId, $users);

    /**
     * Add GitLab project member
     * @param $projectId
     * @param User $user
     * @return mixed
     */
    public function addTeamMember($projectId, User $user);

    /**
     * Remove GitLab project member
     * @param $projectId
     * @param User $user
     * @return mixed
     */
    public function delTeamMember($projectId, User $user);

    /**
     * Retrieve the difference between local DB users and GitLab users
     * @param array $dbUsers
     * @param $autocreate
     * @return mixed
     */
    public function diffUsers(array $dbUsers, $autocreate);

    /**
     * Create GitLab user (ldap formed)
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user);

    /**
     * Retrieve all GitLab users
     * @return mixed
     */
    public function getAllUsers();

    /**
     * Retrieve user
     * @param User $user
     * @return mixed
     */
    public function getUser(User $user);

    /**
     * Delete Gitlab user
     * @param User $user
     * @return mixed
     */
    public function deleteUser(User $user);

    /**
     * Lock user account
     * @param User $user
     * @return mixed
     */
    public function lockUser(User $user);

    /**
     * Unlock user account
     * @param User $user
     * @return mixed
     */
    public function unlockUser(User $user);

    /**
     * Create WebHook on a defined project
     * @param Project $project
     * @param $url
     * @return mixed
     */
    public function setProjectWebHook(Project $project, $url);

    /**
     * Adding a file to a project
     * @param Project $project
     * @param $fileName
     * @param $fileContent
     * @param $commitMessage
     * @param $branch
     * @param string $encoding
     * @return mixed
     */
    public function addFile(Project $project, $fileName, $fileContent, $commitMessage, $branch, $encoding = 'text');

    /**
     * Getting files of a project
     * @param Project $project
     * @return mixed
     */
    public function getTree(Project $project);

    /**
     * @param Project $project
     * @param $filePath
     * @param string $ref
     * @return mixed
     */
    public function getFile(Project $project, $filePath, $ref = 'master');
}