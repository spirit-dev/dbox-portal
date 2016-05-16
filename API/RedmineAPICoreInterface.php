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
 * File           RedmineAPICoreInterface.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Interface RedmineAPICoreInterface
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
interface RedmineAPICoreInterface {

    /**
     * Check if server is available
     * @return mixed
     */
    public function isAvailable();

    /**
     * Get Redmine DB ID for a given username
     * @param User $user
     * @return mixed
     */
    public function getIdByUsername(User $user);

    /**
     * Create redmine user
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user);

    /**
     * Get User informations
     * @param $id
     * @return mixed
     */
    public function showUser($id);

    /**
     * Remove user
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
     * Get a project list
     * @return mixed
     */
    public function listProject();

    /**
     * Show a project details
     * @param Project $project
     * @return mixed
     */
    public function showProject(Project $project);

    /**
     * Create a project
     * @param Project $project
     * @return mixed
     */
    public function createProject(Project $project);

    /**
     * Update project
     * @param Project $project
     * @return mixed
     */
    public function updateProject(Project $project);

    /**
     * delete project
     * @param Project $project
     * @return mixed
     */
    public function deleteProject(Project $project);

    /**
     * Set project members
     * @param Project $project
     * @param $users
     * @param User $owner
     * @return mixed
     */
    public function setProjectMemberships(Project $project, $users, User $owner);

    /**
     * Get members of a project
     * @param Project $project
     * @return mixed
     */
    public function getProjectMemberships(Project $project);

    /**
     * Add member to a project
     * @param Project $project
     * @param User $user
     * @param $role
     * @return mixed
     */
    public function addProjectMemberships(Project $project, User $user, $role);

    /**
     * Remove member from project
     * @param Project $project
     * @param User $user
     * @return mixed
     */
    public function removeProjectMemberships(Project $project, User $user);

    /**
     * Get issues for a project and for a tracker
     * @param Project $project
     * @param null $tracker
     * @return mixed
     */
    public function getIssues(Project $project, $tracker = null);

}