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
 * File           JenkinsAPICore.php
 * Updated the    17/05/16 08:23
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use JenkinsKhan\Jenkins as Jenkins;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class JenkinsAPICore
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
abstract class JenkinsAPICore {

    /**
     *
     */
    const GET = 'get';
    /**
     *
     */
    const POST = 'post';
    
    /**
     * @var
     */
    protected $jenkinsUrl;
    /**
     * @var
     */
    protected $jenkinsProto;
    /**
     * @var
     */
    protected $jenkinsUser;
    /**
     * @var
     */
    protected $jenkinsToken;

    /**
     * @var
     */
    protected $defaultJobName;

    /**
     * @var
     */
    protected $basePath;
    /**
     * @var string
     */
    protected $configFile;
    /**
     * @var string
     */
    protected $configFileBkpFolder;
    /**
     * @var string
     */
    protected $configFileBkpFile;
    /**
     * @var string
     */
    protected $userBasePath;
    /**
     * @var string
     */
    protected $userConfigFileTemplate;

    /**
     * @var Jenkins
     */
    protected $jenkinsClient;
    /**
     * @var bool
     */
    protected $serverAvailable;

    /**
     * GitLabAPI constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        // Applying API connection info
        $this->jenkinsUrl = $container->getParameter("spirit_dev_d_box_portal.jenkins_api.url");
        $this->jenkinsProto = $container->getParameter("spirit_dev_d_box_portal.jenkins_api.protocol");
        $this->jenkinsUser = $container->getParameter("spirit_dev_d_box_portal.jenkins_api.user");
        $this->jenkinsToken = $container->getParameter("spirit_dev_d_box_portal.jenkins_api.token");
        $this->basePath = $container->getParameter("spirit_dev_d_box_portal.jenkins_api.path");
        
        // Important values
        $this->defaultJobName = $container->getParameter("spirit_dev_d_box_portal.jenkins_api.default_pipeline_name");

        // Initialiazing local variables
        $this->configFile = $this->basePath . "config.xml";
        $this->configFileBkpFolder = $this->basePath . "configBkp";
        $this->configFileBkpFile = $this->configFileBkpFolder . "/config.xml.";
        $this->userBasePath = $this->basePath . "users/";
        $this->userConfigFileTemplate = $this->userBasePath . "/config.xml";
        // Checking environment
//        $this->checkEnvironmentConfiguration();

        // Creating client
        $this->jenkinsClient = $this->authenticate();
        // Check availability
        try {
            $this->serverAvailable = $this->jenkinsClient->isAvailable();
        } catch (\Exception $e) {
            $this->serverAvailable = false;
        }
    }

    /**
     * Set authentication to JenkinsAPI
     * @return Jenkins
     */
    protected function authenticate() {
        $jenkinsClient = new Jenkins($this->getUrl());
        return $jenkinsClient;
    }

    /**
     * Define API URL
     * @return string
     */
    protected function getUrl() {
        return $this->jenkinsProto . $this->jenkinsUser . ":" . $this->jenkinsToken . "@" . $this->jenkinsUrl;
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

    /**
     * @param $requestType      $this::GET or $this::POST
     * @param $url              String
     * @param array $params Array
     * @param bool $failOnError true or false
     * @param bool $showHeader true or false
     * @param array $moreOpts
     * @return string
     */
    protected function sendRequest($requestType, $url, array $params = null, $failOnError = true, $showHeader = true, array $moreOpts = array()) {
        $curlOpt = array();
        if ($requestType == $this::GET) {
        } elseif ($requestType == $this::POST) {
            $curlOpt[CURLOPT_POST] = true;

            if ($params != null && count($params) > 0) {
                $paramString = "";
                $i = 0;
                foreach ($params as $key => $value) {
                    if ($i == 0) {
                        $paramString .= $key . "=" . $value;
                    } else {
                        $paramString .= "&" . $key . "=" . $value;
                    }
                    $i++;
                }
                $curlOpt[CURLOPT_POSTFIELDS] = $paramString;
            }
        }

        if ($failOnError) {
            $curlOpt[CURLOPT_FAILONERROR] = true;
        }
        if ($showHeader) {
            $curlOpt[CURLOPT_HEADER] = true;
        }

        if (count($moreOpts) > 0) {
            foreach ($moreOpts as $opt => $val) {
                $curlOpt[$opt] = $val;
            }
        }

        $curlOpt[CURLOPT_RETURNTRANSFER] = 1;

        $response = $this->jenkinsClient->execute($url, $curlOpt);

        return $response;
    }

    /**
     * Backup config file
     * @return bool
     * @deprecated
     */
    protected function backupConfigFile() {

        $fs = new Filesystem();

        $dateNow = new \DateTime();
        $configBkpFile = $this->configFileBkpFile . $dateNow->format("Y-m-d_H-i-s");

        $fs->copy($this->configFile, $configBkpFile);
        $fs->chmod($configBkpFile, 0644);
        $fs->chown($configBkpFile, 'jenkins');
        $fs->chgrp($configBkpFile, 'jenkins');

        return $fs->exists($configBkpFile);
    }

    /**
     * Check environment variables and applies modifications
     * @deprecated
     */
    private function checkEnvironmentConfiguration() {

        $fs = new Filesystem();

        // Creating backup folder if not exists
        if (!$fs->exists($this->configFileBkpFolder)) {
            $fs->mkdir($this->configFileBkpFolder, 0755);
            $fs->chown($this->configFileBkpFolder, 'jenkins');
            $fs->chgrp($this->configFileBkpFolder, 'jenkins');
        }

        // Creating user configuration template if not exists
        if (!$fs->exists($this->userConfigFileTemplate)) {

            $xmlString = '<user><fullName>FULLNAME</fullName><properties><hudson.model.PaneStatusProperties><collapsed/></hudson.model.PaneStatusProperties><jenkins.security.ApiTokenProperty><apiToken></apiToken></jenkins.security.ApiTokenProperty><com.cloudbees.plugins.credentials.UserCredentialsProvider_-UserCredentialsProperty plugin="credentials@1.24"><domainCredentialsMap class="hudson.util.CopyOnWriteMap$Hash"/></com.cloudbees.plugins.credentials.UserCredentialsProvider_-UserCredentialsProperty><hudson.model.MyViewsProperty><views><hudson.model.AllView><owner class="hudson.model.MyViewsProperty" reference="../../.."/><name>All</name><filterExecutors>false</filterExecutors><filterQueue>false</filterQueue><properties class="hudson.model.View$PropertyList"/></hudson.model.AllView></views></hudson.model.MyViewsProperty><hudson.search.UserSearchProperty><insensitiveSearch>false</insensitiveSearch></hudson.search.UserSearchProperty><hudson.tasks.Mailer_-UserProperty plugin="mailer@1.15"><emailAddress>EMAIL</emailAddress></hudson.tasks.Mailer_-UserProperty><jenkins.security.LastGrantedAuthoritiesProperty><roles><string></string></roles><timestamp></timestamp></jenkins.security.LastGrantedAuthoritiesProperty></properties></user>';

            $xml = new \SimpleXMLElement($xmlString);
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;

            $fileToWrite = fopen($this->userConfigFileTemplate, "w");
            fwrite($fileToWrite, $dom->saveXML());
            fclose($fileToWrite);

            $fs->chmod($this->userConfigFileTemplate, 0644);
            $fs->chown($this->userConfigFileTemplate, 'jenkins');
            $fs->chgrp($this->userConfigFileTemplate, 'jenkins');

        }

    }
}