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
 * File           SonarAPI.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SonarAPI
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
class SonarAPI extends SonarAPICore implements SonarAPIInterface {

    /**
     * SonarAPI constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);

        $this->isAuthenticated();
    }

    /**
     * Check if client is authenticated to api
     * @return mixed
     */
    public function isAuthenticated() {
        try {
            $validated = $this->sonarClient->api('authentication')->validate();
            if ($validated['valid'] == true) {
                $this->serverAvailable = true;
            } else {
                $this->serverAvailable = null;
            }
        } catch (\Exception $e) {
            $this->serverAvailable = false;
        }
        return $this->serverAvailable;
    }

    /**
     * Check if server is available
     * @return mixed
     */
    public function isAvailable() {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        $returnVal = null;

        // Sending request
        $request = $this->sonarClient->api('server')->status();

        // Checking if server status
        if (array_key_exists('status', $request)) {
            if ($request['status'] == 'UP') {
                $returnVal = true;
            } else {
                $returnVal = false;
            }
        }

        return $returnVal;
    }

    /**
     * Create user
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('users')->create(
                array(
                    "login" => $user->getUsername(),
                    "name" => $user->getCommonName(),
                    "password" => 'azerty',
                    "password_confirmation" => 'azerty',
                    "email" => $user->getEmail()
                ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Delete user
     * @param User $user
     * @return mixed
     */
    public function deleteUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            $this->sonarClient->api('users')->deactivate($user->getUsername());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Deactivate user
     * @param User $user
     * @return mixed
     */
    public function deactivateUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            $this->sonarClient->api('users')->deactivate($user->getUsername());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Activate user
     * @param User $user
     * @return mixed
     */
    public function activateUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            $issue = $this->sonarClient->api('users')->update(
                array(
                    "login" => $user->getUsername(),
                    "name" => $user->getCommonName(),
                    "password" => 'azerty',
                    "password_confirmation" => 'azerty',
                    "active" => true,
                    "email" => $user->getEmail()
                )
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create project
     * @param Project $project
     * @return mixed
     */
    public function createProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('projects')->create(
                array(
                    "key" => $project->getCanonicalName(),
                    "name" => $project->getName(),
                    "branch" => 'dev'
                ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Delete project
     * @param Project $project
     * @return mixed
     */
    public function deleteProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('projects')->deleteProject(
                array(
                    "id" => $project->getSonarProjectId()
                ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Adds permissions to project for a user
     * @param User $user
     * @param Project $project
     * @return mixed
     */
    public function addPermission(User $user, Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('permissions')->add(
                array(
                    "component" => $project->getSonarProjectKey(),
                    "user" => $user->getUsername(),
                    "permission" => 'user'
                ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Removes a permission from a project
     * @param User $user
     * @param Project $project
     * @return mixed
     */
    public function removePermission(User $user, Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('permissions')->remove(
                array(
                    "component" => $project->getSonarProjectKey(),
                    "user" => $user->getUsername(),
                    "permission" => 'user'
                ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get issues of a defined project per resolution
     * @param Project $project
     * @param $resolved
     * @return mixed
     */
    public function getIssuesPerResolution(Project $project, $resolved) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('issues')->search(
                array(
                    "componentRoots" => $project->getSonarProjectKey(),
                    "resolved" => $resolved
                ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get issues of a defined project per severity
     * @param Project $project
     * @param $severity
     * @return mixed
     */
    public function getIssuesPerSeverity(Project $project, $severity) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->sonarClient->api('issues')->search(
                array(
                    "componentRoots" => $project->getSonarProjectKey(),
                    "severities" => $severity
                ));
        } catch (\Exception $e) {
            return null;
        }
    }
}