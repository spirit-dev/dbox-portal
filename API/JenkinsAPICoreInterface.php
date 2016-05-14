<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\ContinuousIntegration;
use Proxies\__CG__\SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
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