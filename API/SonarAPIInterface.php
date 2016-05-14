<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Interface SonarAPIInterface
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
interface SonarAPIInterface {

    /**
     * Check if client is authenticated to api
     * @return mixed
     */
    public function isAuthenticated();

    /**
     * Check server is up
     * @return mixed
     */
    public function isAvailable();

    /**
     * Create user
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user);

    /**
     * Delete user
     * @param User $user
     * @return mixed
     */
    public function deleteUser(User $user);

    /**
     * Deactivate user
     * @param User $user
     * @return mixed
     */
    public function deactivateUser(User $user);

    /**
     * Activate user
     * @param User $user
     * @return mixed
     */
    public function activateUser(User $user);

    /**
     * Create project
     * @param Project $project
     * @return mixed
     */
    public function createProject(Project $project);

    /**
     * Delete project
     * @param Project $project
     * @return mixed
     */
    public function deleteProject(Project $project);

    /**
     * Adds permissions to project for a user
     * @param User $user
     * @param Project $project
     * @return mixed
     */
    public function addPermission(User $user, Project $project);

    /**
     * Removes a permission from a project
     * @param User $user
     * @param Project $project
     * @return mixed
     */
    public function removePermission(User $user, Project $project);

    /**
     * Get issues of a defined project per resolution
     * @param Project $project
     * @param $resolved
     * @return mixed
     */
    public function getIssuesPerResolution(Project $project, $resolved);

    /**
     * Get issues of a defined project per severity
     * @param Project $project
     * @param $severity
     * @return mixed
     */
    public function getIssuesPerSeverity(Project $project, $severity);

}