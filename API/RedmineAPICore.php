<?php

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
    const ROLE_DEV = 3;
    /**
     *
     */
    const ROLE_MANAGER = 4;
    /**
     *
     */
    const PM_MODULES = [
        'issue_tracking',
        'time_tracking',
        'news',
        'documents',
        'files',
        'wiki',
        'repository',
        'boards',
        'calendar',
        'gantt',
        'agile',
//        'dashboard',
        'dmsf',
        'favorite_projects',
        'monitoring_controlling_project'
    ];

    const BUG_TRACKER = 2;
    const EVOL_TRACKER = 3;
    const TEST_TRACKER = 3;
    const QA_TRACKER = 3;

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
    const API_ISSUE = 'issue';
    /**
     *
     */
    const AUTH_SOURCE_ID = 1;

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
        $this->redmineUrl = $container->getParameter('redmine_api')['url'];
        $this->redmineToken = $container->getParameter('redmine_api')['token'];
        $this->redminePort = $container->getParameter('redmine_api')['port'];

        $this->router = $container->get('router');

        // Initialize connection
        $this->redmineClient = $this->authenticate();
    }

    /**
     * Authenticate to remote API
     * @return Redmine
     */
    protected function authenticate() {
        $redmine = new Redmine($this->redmineUrl, $this->redmineToken);
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
        $curlOptions[CURLOPT_URL] = $this->redmineUrl . $url;
        $curlOptions[CURLOPT_HTTPHEADER] = $defaultHeader;

        if ($type == 'post') {
            $curlOptions[CURLOPT_POST] = 1;
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