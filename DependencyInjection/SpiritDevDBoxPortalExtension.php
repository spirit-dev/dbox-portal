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
 * File           SpiritDevDBoxPortalExtension.php
 * Updated the    26/05/16 15:25
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SpiritDevDBoxPortalExtension extends Extension {
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container) {
        $configuration = new Configuration();
        $config = array();
        foreach ($configs as $subConfig) {
            $config = array_merge($config, $subConfig);
        }
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // Checking LDAP Drivers configuration

        $this->checkConfig($container, $config, 'ldap_driver', 'ldap_driver', false);
        $this->checkConfig($container, $config['ldap_driver'], 'driver', 'ldap_driver.driver', false);
        $this->checkConfig($container, $config['ldap_driver']['driver'], 'host', 'ldap_driver.driver.host', true);
        $this->checkConfig($container, $config['ldap_driver']['driver'], 'port', 'ldap_driver.driver.port', true);
        $this->checkConfig($container, $config['ldap_driver']['driver'], 'username', 'ldap_driver.driver.username', true);
        $this->checkConfig($container, $config['ldap_driver']['driver'], 'password', 'ldap_driver.driver.password', true);
        $this->checkConfig($container, $config['ldap_driver'], 'user', 'ldap_driver.user', false);
        $this->checkConfig($container, $config['ldap_driver']['user'], 'basedn', 'ldap_driver.user.basedn', true);
        $this->checkConfig($container, $config['ldap_driver'], 'provider', 'ldap_driver.provider', true);

        // Checking Applicative configuration
        $this->checkConfig($container, $config, 'app', 'app', false);
        $this->checkConfig($container, $config['app'], 'admin_mail', 'app.admin_mail', true);
        $this->checkConfig($container, $config['app'], 'from_mail', 'app.from_mail', true);
        $this->checkConfig($container, $config['app'], 'subject_prepend', 'app.subject_prepend', true);

        // Checking GitLab configuration
        $this->checkConfig($container, $config, 'gitlab_api', 'gitlab_api', false);
        $this->checkConfig($container, $config['gitlab_api'], 'url', 'gitlab_api.url', true);
        $this->checkConfig($container, $config['gitlab_api'], 'token', 'gitlab_api.token', true);

        // Checking Jenkins configuration
        $this->checkConfig($container, $config, 'jenkins_api', 'jenkins_api', false);
        $this->checkConfig($container, $config['jenkins_api'], 'url', 'jenkins_api.url', true);
        $this->checkConfig($container, $config['jenkins_api'], 'protocol', 'jenkins_api.protocol', true);
        $this->checkConfig($container, $config['jenkins_api'], 'user', 'jenkins_api.user', true);
        $this->checkConfig($container, $config['jenkins_api'], 'token', 'jenkins_api.token', true);
        $this->checkConfig($container, $config['jenkins_api'], 'password', 'jenkins_api.password', true);
        $this->checkConfig($container, $config['jenkins_api'], 'path', 'jenkins_api.path', true);
        $this->checkConfig($container, $config['jenkins_api'], 'default_pipeline_name', 'jenkins_api.default_pipeline_name', true);

        // Checking Redmine configuration
        $this->checkConfig($container, $config, 'redmine_api', 'redmine_api', false);
        $this->checkConfig($container, $config['redmine_api'], 'url', 'redmine_api.url', true);
        $this->checkConfig($container, $config['redmine_api'], 'protocol', 'redmine_api.protocol', true);
        $this->checkConfig($container, $config['redmine_api'], 'port', 'redmine_api.port', true);
        $this->checkConfig($container, $config['redmine_api'], 'token', 'redmine_api.token', true);
        
        $this->checkConfig($container, $config['redmine_api'], 'role_dev', 'redmine_api.role_dev', true);
        $this->checkConfig($container, $config['redmine_api'], 'role_manager', 'redmine_api.role_manager', true);
        $this->checkConfig($container, $config['redmine_api'], 'pm_modules', 'redmine_api.pm_modules', true);
        $this->checkConfig($container, $config['redmine_api'], 'bug_tracker', 'redmine_api.bug_tracker', true);
        $this->checkConfig($container, $config['redmine_api'], 'evol_tracker', 'redmine_api.evol_tracker', true);
        $this->checkConfig($container, $config['redmine_api'], 'test_tracker', 'redmine_api.test_tracker', true);
        $this->checkConfig($container, $config['redmine_api'], 'qa_tracker', 'redmine_api.qa_tracker', true);
        $this->checkConfig($container, $config['redmine_api'], 'auth_source', 'redmine_api.auth_source', true);

        // Checking Sonar configuration
        $this->checkConfig($container, $config, 'sonar_api', 'sonar_api', false);
        $this->checkConfig($container, $config['sonar_api'], 'url', 'sonar_api.url', true);
        $this->checkConfig($container, $config['sonar_api'], 'username', 'sonar_api.username', true);
        $this->checkConfig($container, $config['sonar_api'], 'password', 'sonar_api.password', true);

    }

    /**
     * @param ContainerBuilder $container
     * @param $configurationToCheck
     * @param $commonName
     */
    private function checkConfig(ContainerBuilder $container, $configurationToCheck, $key, $commonName, $toSet) {

        if (!isset($configurationToCheck[$key])) {
            if ($toSet) {
                $message = 'The "' . $commonName . '" option must be set';
            } else {
                $message = 'The "' . $commonName . '" option node must be set';
            }
            throw new \InvalidArgumentException($message);
        } else {
            if ($toSet) {
                $container->setParameter('spirit_dev_d_box_portal.'.$commonName, $configurationToCheck[$key]);
            }
        }
    }

    /**
     * @return string
     */
    public function getXsdValidationBasePath() {
        return __DIR__ . '/../Resources/config/';
    }

    /**
     * @return string
     */
    public function getNamespace() {
        return 'http://www.example.com/symfony/schema/';
    }
}
