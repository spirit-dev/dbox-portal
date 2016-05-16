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
 * File           JenkinsAPICoreInterface.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use Proxies\__CG__\SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\ContinuousIntegration;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Interface JenkinsAPICoreInterface
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
interface JenkinsAPICoreInterface {

    /**
     * Check if jenkins is available
     * @return mixed
     */
    public function isAvailable();

    /**
     * Create User
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user);

    /**
     * Delete User
     * @param User $user
     * @return mixed
     */
    public function deleteUser(User $user);

    /**
     * Restart Jenkins server
     * @param bool $force
     * @return mixed
     */
    public function restartHost($force = false);

    /**
     * Reload host
     * @return mixed
     */
    public function reloadHost();

    /**
     * Copy a job
     * @param $to
     * @param $from
     * @return mixed
     */
    public function copyJob($to, $from);

    /**
     * Deletes a job
     * @param $jobName
     * @return mixed
     */
    public function deleteJob($jobName);

    /**
     * Create view
     * @param $viewName
     * @return mixed
     */
    public function createView($viewName);

    /**
     * Add a defined job to a defined view
     * @param $viewName
     * @param $jobName
     * @return mixed
     */
    public function addJobToView($viewName, $jobName);

    /**
     * Deletes a view
     * @param $viewName
     * @return mixed
     */
    public function deleteView($viewName);

    /**
     * Get Builds for a job
     * @param ContinuousIntegration $ci
     * @return mixed
     */
    public function getBuilds(ContinuousIntegration $ci);

    /**
     * Launch remote job
     * @param ContinuousIntegration $ci
     * @return mixed
     */
    public function launchJob(ContinuousIntegration $ci);

    /**
     * Get remote job progression
     * @param ContinuousIntegration $ci
     * @return mixed
     */
    public function getProgression(ContinuousIntegration $ci);
}