<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Class RedmineAPI
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
class RedmineAPI extends RedmineAPICore implements RedmineAPICoreInterface {

    /**
     * RedmineAPI constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        parent::__construct($container);
        // Define server availability
        $this->serverAvailable = $this->isAvailable();
    }

    /**
     * Check if server is available
     * @return mixed
     */
    public function isAvailable() {
        try {
            $this->redmineClient->api($this::API_USER)->getIdByUsername('admin');
            $responseCode = $this->redmineClient->getResponseCode();
            if ($responseCode == 200) {
                return true;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Redmine DB ID for a given username
     * @param User $user
     * @return mixed
     */
    public function getIdByUsername(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        return $this->redmineClient->api($this::API_USER)->getIdByUsername($user->getUsername());
    }

    /**
     * Create redmine user
     * @param User $user
     * @return mixed
     */
    public function createUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        $params['login'] = $user->getUsername();
        $params['firstname'] = $user->getFirstName();
        $params['lastname'] = $user->getLastName();
        $params['mail'] = $user->getEmail();
        $params['auth_source_id'] = $this::AUTH_SOURCE_ID;

        return $this->redmineClient->api($this::API_USER)->create($params);

    }

    /**
     * Get User informations
     * @param $id
     * @return mixed
     */
    public function showUser($id) {
        return $this->redmineClient->api($this::API_USER)->show($id);
    }

    /**
     * Remove user
     * @param User $user
     * @return mixed
     */
    public function deleteUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }
        $this->redmineClient->api($this::API_USER)->remove($user->getRedmineId());
        return true;
    }

    /**
     * Lock user account
     * @param User $user
     * @return mixed
     */
    public function lockUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        // Create curl
//        $curl = curl_init();
//        $curlOptions[CURLOPT_URL] = $this->redmineUrl . "/users/" . $user->getRedmineId() . ".json?user[status]=3";
//        $curlOptions[CURLOPT_HTTPHEADER] = array(
//            'X-Redmine-API-Key: ' . $this->redmineToken
//        );
//        $curlOptions[CURLOPT_POST] = 1;
//        $curlOptions[CURLOPT_POSTFIELDS] = array('_method' => 'put');
//        curl_setopt_array($curl, $curlOptions);
//        // Send curl
//        curl_exec($curl);
//        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//        // Return
//        if ($responseCode >= 200 && $responseCode < 300) {
//            return true;
//        } else {
//            return false;
//        }

        return $this->sendRequest(
            "/users/" . $user->getRedmineId() . ".json?user[status]=3",
            array('_method' => 'put'),
            'post'
        );
    }

    /**
     * Unlock user account
     * @param User $user
     * @return mixed
     */
    public function unlockUser(User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

//        // Create curl
//        $curl = curl_init();
//        $curlOptions[CURLOPT_URL] = $this->redmineUrl . "/users/" . $user->getRedmineId() . ".json?user[status]=1";
//        $curlOptions[CURLOPT_HTTPHEADER] = array(
//            'X-Redmine-API-Key: ' . $this->redmineToken
//        );
//        $curlOptions[CURLOPT_POST] = 1;
//        $curlOptions[CURLOPT_POSTFIELDS] = array('_method' => 'put');
//        curl_setopt_array($curl, $curlOptions);
//        // Send curl
//        curl_exec($curl);
//        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//        curl_close($curl);
//        // Return
//        if ($responseCode >= 200 && $responseCode < 300) {
//            return true;
//        } else {
//            return false;
//        }

        return $this->sendRequest(
            "/users/" . $user->getRedmineId() . ".json?user[status]=1",
            array('_method' => 'put'),
            'post'
        );
    }

    /**
     * Get a project list
     * @return mixed
     */
    public function listProject() {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        return $this->redmineClient->api($this::API_PROJECT)->listing(true);
    }

    /**
     * Show a project details
     * @param Project $project
     * @return mixed
     */
    public function showProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        return $this->redmineClient->api($this::API_PROJECT)->show($project->getRedmineId());
    }

    /**
     * Create a project
     * @param Project $project
     * @return mixed
     */
    public function createProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        $xml = new \SimpleXMLElement('<?xml version="1.0"?><project></project>');
        $xml->addChild('name', $project->getName());
        $xml->addChild('identifier', $this->defineIdentifier($project->getName()));
        $xml->addChild('description', $project->getDescription());
        $xml->addChild('homepage', $this->defineHomepage($project->getName()));
        $xml->addChild('is_public', 'false');

        // Adding modules
        for ($i = 0; $i < count($this::PM_MODULES); $i++) {
            $xml->addChild('enabled_module_names', $this::PM_MODULES[$i]);
        }

        return $this->sendRequest(
            "/projects.xml",
            $xml->asXML(),
            'post',
            array('Content-Type: text/xml'),
            true,
            array(
                CURLOPT_VERBOSE => 0,
                CURLOPT_HEADER => 0,
                CURLOPT_RETURNTRANSFER => 1
            )
        );
    }

    /**
     * Update project
     * @param Project $project
     * @return mixed
     */
    public function updateProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }
    }

    /**
     * delete project
     * @param Project $project
     * @return mixed
     */
    public function deleteProject(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        return $this->redmineClient->api($this::API_PROJECT)->remove($project->getRedmineProjectId());
    }

    /**
     * Set project members
     * @param Project $project
     * @param $users
     * @param User $owner
     * @return array|null
     */
    public function setProjectMemberships(Project $project, $users, User $owner) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        $returnArray = array();

        // Adding users with developper role
        foreach ($users as $user) {
            if ($user != $owner) {
                $returnArray[] = $this->addProjectMemberships($project, $user, $this::ROLE_DEV);
            }
        }

        // Adding project owner
        $returnArray[] = $this->addProjectMemberships($project, $owner, $this::ROLE_MANAGER);

        return $returnArray;
    }

    /**
     * Add member to a project
     * @param Project $project
     * @param User $user
     * @param $role
     * @return mixed
     */
    public function addProjectMemberships(Project $project, User $user, $role) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->redmineClient->api($this::API_MEMBERSHIP)->create($project->getRedmineProjectId(), array(
                'user_id' => $user->getRedmineId(),
                'role_ids' => array($role)
            ));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Remove member from project
     * @param Project $project
     * @param User $user
     * @return mixed
     */
    public function removeProjectMemberships(Project $project, User $user) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            // Get first the membership ID
            $membershipId = null;
            $membershipArray = $this->getProjectMemberships($project);
            $membershipArray = $membershipArray['memberships'];
            foreach ($membershipArray as $membership) {
                if ($membership['user']['id'] == $user->getRedmineId()) {
                    $membershipId = $membership['id'];
                }
            }
            // Remove it
            return $this->redmineClient->api($this::API_MEMBERSHIP)->remove($membershipId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get members of a project
     * @param Project $project
     * @return mixed
     */
    public function getProjectMemberships(Project $project) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->redmineClient->api($this::API_MEMBERSHIP)->all($project->getRedmineProjectId());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Remove member from project
     * @param Project $project
     * @param null $tracker
     * @return null
     */
    public function getIssues(Project $project, $tracker = null) {
        // Fast Return in case of server stopped
        if (!$this->serverAvailable) {
            return null;
        }

        try {
            return $this->redmineClient->api($this::API_ISSUE)->all(array(
                'project_id' => $project->getRedmineProjectId(),
                'tracker_id' => $tracker
            ));
        } catch (\Exception $e) {
            return null;
        }
    }
}