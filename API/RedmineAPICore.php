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
 * File           RedmineAPICore.php
 * Updated the    29/07/16 10:36
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use Redmine\Client as Redmine;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RedmineAPICore
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
abstract class RedmineAPICore {

    /**
     *
     */
    const API_USER = 'user';
    /**
     *
     */
    const API_MEMBERSHIP = 'membership';
    /**
     *
     */
    const API_PROJECT = 'project';
    /**
     *
     */
    const API_ISSUE = 'issue';
    /**
     * @var
     */
    protected $roleDev;
    /**
     * @var
     */
    protected $roleManager;
    /**
     * @var
     */
    protected $pmModules;
    /**
     * @var
     */
    protected $trackerBug;
    /**
     * @var
     */
    protected $trackerEvol;
    /**
     * @var
     */
    protected $trackerTest;
    /**
     * @var
     */
    protected $trackerQa;
    /**
     * @var
     */
    protected $authSourceId;
    /**
     * @var
     */
    protected $redmineProtocol;

    /**
     * @var
     */
    protected $redmineUrl;
    /**
     * @var
     */
    protected $redmineToken;
    /**
     * @var
     */
    protected $redminePort;
    /**
     * @var
     */
    protected $sslVerify;

    /**
     * @var Redmine
     */
    protected $redmineClient;
    /**
     * @var
     */
    protected $serverAvailable;
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * RedmineAPICore constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        // Getting datas
        $this->redmineProtocol = $container->getParameter('spirit_dev_d_box_portal.redmine_api.protocol');
        $this->redmineUrl = $container->getParameter('spirit_dev_d_box_portal.redmine_api.url');
        $this->redmineToken = $container->getParameter('spirit_dev_d_box_portal.redmine_api.token');
        $this->redminePort = $container->getParameter('spirit_dev_d_box_portal.redmine_api.port');
        $this->sslVerify = $container->getParameter('spirit_dev_d_box_portal.redmine_api.ssl_verify');

        // Setting constants
        $this->roleDev = $container->getParameter('spirit_dev_d_box_portal.redmine_api.role_dev');
        $this->roleManager = $container->getParameter('spirit_dev_d_box_portal.redmine_api.role_manager');
        $this->pmModules = $container->getParameter('spirit_dev_d_box_portal.redmine_api.pm_modules');
        $this->trackerBug = $container->getParameter('spirit_dev_d_box_portal.redmine_api.bug_tracker');
        $this->trackerEvol = $container->getParameter('spirit_dev_d_box_portal.redmine_api.evol_tracker');
        $this->trackerTest = $container->getParameter('spirit_dev_d_box_portal.redmine_api.test_tracker');
        $this->trackerQa = $container->getParameter('spirit_dev_d_box_portal.redmine_api.qa_tracker');
        $this->authSourceId = $container->getParameter('spirit_dev_d_box_portal.redmine_api.auth_source');

        // Setting router
        $this->router = $container->get('router');

        // Initialize connection
        $this->redmineClient = $this->authenticate();
    }

    /**
     * Authenticate to remote API
     * @return Redmine
     */
    protected function authenticate() {
        $redmine = new Redmine($this->redmineProtocol . $this->redmineUrl, $this->redmineToken);
        $redmine->setPort($this->redminePort);
        return $redmine;
    }

    /**
     * Define project identifier
     * @param $projectName
     * @return mixed
     */
    protected function defineIdentifier($projectName) {

        $lowercase = strtolower($projectName);
        $vowels = array(' ', '-');
        $identifier = str_replace($vowels, '_', $lowercase);
        return $identifier;
    }

    /**
     * Define project EcosystemV2 homepage
     * @param $projectName
     * @return string
     */
    protected function defineHomepage($projectName) {
        $url = $this->router->generate('spirit_dev_dbox_portal_bundle_project', array('pjt_name' => $projectName), true);
        return $url;
    }

    /**
     * Send a manually defined curl request
     * @param $url
     * @param $datas
     * @param $type
     * @param null $moreHeaders
     * @param bool $xmlResponse
     * @param array $moreOpts
     * @return bool|null
     */
    protected function sendRequest($url, $datas, $type, $moreHeaders = null, $xmlResponse = false, array $moreOpts = array()) {

        $defaultHeader = array(
            'X-Redmine-API-Key: ' . $this->redmineToken
        );
        if ($moreHeaders != null) {
            $defaultHeader = array_merge($defaultHeader, $moreHeaders);
        }

        // Create curl
        $curl = curl_init();
        $curlOptions[CURLOPT_URL] = $this->redmineProtocol . $this->redmineUrl . $url;
        $curlOptions[CURLOPT_HTTPHEADER] = $defaultHeader;
        $curlOptions[CURLOPT_SSL_VERIFYPEER] = $this->sslVerify ? 64 : 0;

        if ($type == 'post') {
            $curlOptions[CURLOPT_POST] = 1;
            $curlOptions[CURLOPT_POSTFIELDS] = $datas;
        } else {
            $curlOptions[CURLOPT_CUSTOMREQUEST] = $type;
            $curlOptions[CURLOPT_POSTFIELDS] = $datas;
        }

        // More headers
        if (count($moreOpts) > 0) {
            foreach ($moreOpts as $opt => $val) {
                $curlOptions[$opt] = $val;
            }
        }

        curl_setopt_array($curl, $curlOptions);

        // Send curl
        if ($xmlResponse) {
            $response = trim(curl_exec($curl));
            $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
            curl_close($curl);

            // Return
            if ($response) {
                // if response is XML, return an SimpleXMLElement object
                if (0 === strpos($contentType, 'application/xml')) {
                    return new \SimpleXMLElement($response);
                }

                return $response;
            } else {
                return null;
            }
        } else {
            curl_exec($curl);
            $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // Return
            if ($responseCode >= 200 && $responseCode < 300) {
                return true;
            } else {
                return false;
            }
        }
    }

}