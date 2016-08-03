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
 * File           JenkinsAPI.php
 * Updated the    03/08/16 17:23
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\ContinuousIntegration;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class JenkinsAPI
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
class JenkinsAPI extends JenkinsAPICore implements JenkinsAPICoreInterface {

    /**
     * JenkinsAPI constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
    }

    /**
     * Check if jenkins is available
     * @return mixed
     */
    public function isAvailable() {
        return $this->serverAvailable;
    }

    /**
     * Create User
     * @param User $user
     * @return mixed
     * @deprecated
     */
    public function createUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        // Initialize values
        $userPathFolder = $this->userBasePath . $user->getUsername();
        $fs = new Filesystem();

        // Create user dir
        try {
            $fs->mkdir($userPathFolder, 0755);
            $fs->chown($userPathFolder, 'jenkins');
            $fs->chgrp($userPathFolder, 'jenkins');
        } catch (IOExceptionInterface $e) {
//            dump("An error occurred while creating your directory at " . $e->getPath());
        }
        // Updating return values
        $retval['userPath'] = $userPathFolder;
        $retval['userPathCreated'] = $fs->exists($userPathFolder);

        // Creating config file
        $userConfigFile = $userPathFolder . "/config.xml";
        if ($fs->exists($this->userConfigFileTemplate) && $fs->exists($userPathFolder)) {
            // Opening config template
            $xmlTemplate = simplexml_load_file($this->userConfigFileTemplate);
            // Overriding some values
            $xmlTemplate->fullName = $user->getCommonName();
            $xmlTemplate->properties->{'hudson.tasks.Mailer_-UserProperty'}->emailAddress = $user->getEmail();
            // Writing new file in user space
            $dom = dom_import_simplexml($xmlTemplate)->ownerDocument;
            $dom->formatOutput = true;
            $fileToWrite = fopen($userConfigFile, "w");
            fwrite($fileToWrite, $dom->saveXML());
            fclose($fileToWrite);
            $fs->chmod($userConfigFile, 0644);
            $fs->chown($userConfigFile, 'jenkins');
            $fs->chgrp($userConfigFile, 'jenkins');
            // Updating return values
            $retval['templateExists'] = true;
            $retval['userConfFile'] = $fs->exists($userConfigFile);
        } else {
            $retval['userConfFile'] = false;
            $retval['templateExists'] = false;
        }

        // Updating Jenkins global rights
        if (file_exists($this->configFile)) {
            // Config file backup
            $retval['confFileBackuped'] = $this->backupConfigFile();
            // Opening config file
            $xmlFile = simplexml_load_file($this->configFile);
            // Overriding some values
            $authorizations = $xmlFile->authorizationStrategy;
            $authorizations->addChild("permission", "hudson.model.Hudson.Read:" . $user->getUsername());
            // Overriding config file
            $dom = dom_import_simplexml($xmlFile)->ownerDocument;
            $dom->formatOutput = true;
            $fileToWrite = fopen($this->configFile, "w");
            fwrite($fileToWrite, $dom->saveXML());
            fclose($fileToWrite);
            // Updating return values
            $retval['confFileUpdated'] = $fs->exists($this->configFile);
        } else {
            $retval['confFileBackuped'] = false;
            $retval['confFileUpdated'] = false;
        }

        // Restarting jenkins
        $retval['hostRestarted'] = $this->reloadHost();

        // Return values
        if ($retval['userPathCreated'] && $retval['confFileUpdated'] && $retval['userConfFile']) {
            return true;
        } else {
            return false;
        }
//        return $retval;
    }

    /**
     * Reload host
     * @return mixed
     */
    public function reloadHost() {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $response = $this->sendRequest($this::POST, "reload");

        return $response;
    }

    /**
     * Delete User
     * @param User $user
     * @return mixed
     * @deprecated
     */
    public function deleteUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        // Initialize values
        $userPath = $this->userBasePath . $user->getUsername();
        $fs = new Filesystem();

        // Delete user path
        $fs->remove($userPath);
        $retval['userPath'] = $userPath;
        $retval['userPathRemoved'] = !$fs->exists($userPath);

        // Remove global var
        if (file_exists($this->configFile)) {
            // Config file backup
            $retval['confFileBackuped'] = $this->backupConfigFile();

            // Opening config file
            $xmlFile = simplexml_load_file($this->configFile);
//            // Removing some values
            $permissions = $xmlFile->xpath("//authorizationStrategy/permission");
            foreach ($permissions as $result) {
                if ($result == "hudson.model.Hudson.Read:" . $user->getUsername()) {
                    unset ($result[0]);
                }
            }
            // Overriding config file
            $dom = dom_import_simplexml($xmlFile)->ownerDocument;
            $dom->formatOutput = true;
            $fileToWrite = fopen($this->configFile, "w");
            fwrite($fileToWrite, $dom->saveXML());
            fclose($fileToWrite);

            // Updating return values
            $retval['confFileUpdated'] = $fs->exists($this->configFile);

        } else {
            $retval['confFileBackuped'] = false;
            $retval['confFileUpdated'] = false;
        }

        // Restart jenkins
        $retval['hostRestarted'] = $this->reloadHost();

        // Return values
        if ($retval['userPathRemoved'] && $retval['confFileUpdated']) {
            return true;
        } else {
            return false;
        }
//        return $retval;
    }

    /**
     * Restart Jenkins server
     * @param bool $force
     * @return mixed
     */
    public function restartHost($force = false) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        if ($force) {
            $response = $this->sendRequest($this::POST, "restart");
        } else {
            $response = $this->sendRequest($this::POST, "safeRestart");
        }

        return $response;
    }

    /**
     * Copy a job
     * @param $to
     * @param $from
     * @return mixed
     */
    public function copyJob($to, $from = null) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        if ($from == null) {
            $from = $this->defaultJobName;
        }

        $response = $this->sendRequest($this::POST, "createItem", array(
                "name" => $to,
                "mode" => "copy",
                "from" => $from
            )
        );

        return $response;
    }

    /**
     * Deletes a job
     * @param $jobName
     * @return mixed
     */
    public function deleteJob($jobName) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $url = sprintf('job/%s/doDelete', $jobName);

        try {
            $response = $this->sendRequest($this::POST, $url);
            if (strpos($response, '302') != false) {
                return true;
            } else if (strpos($response, 'Error') != false) {
                return false;
            }
            return false;
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Create view
     * @param $viewName
     * @return mixed
     */
    public function createView($viewName) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $response = $this->sendRequest($this::POST, "createView", array(
                "name" => $viewName,
                "mode" => "hudson.model.ListView",
                "json" => '{"name": "' . $viewName . '", "mode": "hudson.model.ListView"}'
            )
        );

        return $response;
    }

    /**
     * Add a defined job to a defined view
     * @param $viewName
     * @param $jobName
     * @return mixed
     */
    public function addJobToView($viewName, $jobName) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $response = $this->sendRequest($this::POST, "view/" . $viewName . "/addJobToView", array(
                "name" => $jobName
            )
        );

        return $response;
    }

    /**
     * Deletes a view
     * @param $viewName
     * @return mixed
     */
    public function deleteView($viewName) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $url = sprintf('view/%s/doDelete', $viewName);

        try {
            $response = $this->sendRequest($this::POST, $url);
            return $response;
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get Builds for a job
     * @param ContinuousIntegration $ci
     * @return mixed
     */
    public function getBuilds(ContinuousIntegration $ci) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $job = $this->jenkinsClient->getJob($ci->getCiName());
        $builds = $job->getBuilds();
        return $builds;
    }

    /**
     * Launch remote job
     * @param ContinuousIntegration $ci
     * @return mixed
     */
    public function launchJob(ContinuousIntegration $ci) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        if (!$ci->isParametrized()) {
            $job = $this->jenkinsClient->launchJob($ci->getCiName());
        } else {
            $job = $this->jenkinsClient->launchJob($ci->getCiName(), $ci->getParameters());
        }
        return $job;
    }

    /**
     * Get remote job progression
     * @param ContinuousIntegration $ci
     * @return mixed
     */
    public function getProgression(ContinuousIntegration $ci) {
        // Fast Return in case of server stopped
        if (!$this->isServerAvailable()) {
            return null;
        }

        $build = $this->jenkinsClient->getJob($ci->getCiName())->getBuilds()[0];
        $progress = $build->getProgress();
//        dump($progress);

        return $progress;
    }

}