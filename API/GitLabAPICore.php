<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use Gitlab\Client as GitLab;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class GitLabAPI
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
abstract class GitLabAPICore {

    /**
     *
     */
    const API_PROJECTS = 'projects';
    /**
     *
     */
    const API_USERS = 'users';
    /**
     *
     */
    const API_REPOSITORIES = 'repositories';
    /**
     * @var GitLab
     */
    protected $gitLabClient;
    /**
     * @var
     */
    protected $gitLabUrl;
    /**
     * @var
     */
    protected $gitLabToken;
    /**
     * @var
     */
    protected $ldapProvider;
    /**
     * @var
     */
    protected $serverAvailable;

    /**
     * GitLabAPI constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        // Applying API connection info
        $this->gitLabUrl = $container->getParameter("gitlab_api")["url"];
        $this->gitLabToken = $container->getParameter("gitlab_api")["token"];
        $this->ldapProvider = $container->getParameter("ldap_driver")["provider"];
        // Creating client
        $this->gitLabClient = $this->authenticate();
    }

    /**
     * @return \Gitlab\Client
     */
    protected function authenticate() {
        $client = new GitLab($this->gitLabUrl);
        $client->authenticate($this->gitLabToken, GitLab::AUTH_URL_TOKEN);
        return $client;
    }

    /**
     * @param array $dbUsers
     * @param array $gitLabUsers
     * @return array
     */
    protected function getDiffUsers(array $dbUsers, array $gitLabUsers) {
        $diffUser = array();
        // Loop on dbUsers
        foreach ($dbUsers as $dbUser) {
            // Loop on gitlab users
            $userInGitLabList = false;
            for ($i = 0; $i < count($gitLabUsers); $i++) {
                $gitLabUser = $gitLabUsers[$i];
                // If user is not admin
                if (!$gitLabUser['is_admin'] && $gitLabUser['username'] == $dbUser->getUsername()) {
                    $userInGitLabList = true;
                }
            }
            if (!$userInGitLabList) {
                $diffUser[] = $dbUser;
            }
        }
        // Return list
        return $diffUser;
    }

    /**
     * Function return a compliant value of server availability
     *
     * @return bool
     */
    protected function isServerAvailable() {
        if (!$this->serverAvailable || $this->serverAvailable == null) {
            return false;
        } else {
            return true;
        }
    }
}