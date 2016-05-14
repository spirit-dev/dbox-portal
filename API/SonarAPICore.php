<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\API;

use SonarQube\Client as Sonar;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SonarAPICore
 * @package SpiritDev\Bundle\DBoxPortalBundle\API
 */
class SonarAPICore {

    /**
     * @var
     */
    protected $sonarUrl;
    /**
     * @var
     */
    protected $sonarUser;
    /**
     * @var
     */
    protected $sonarPass;

    /**
     * @var Sonar
     */
    protected $sonarClient;

    /**
     * @var
     */
    protected $serverAvailable;

    /**
     * SonarAPICore constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {

        $this->sonarUrl = $container->getParameter('sonar_api')['url'];
        $this->sonarUser = $container->getParameter('sonar_api')['username'];
        $this->sonarPass = $container->getParameter('sonar_api')['password'];

        $this->sonarClient = $this->authenticate();
    }

    /**
     * @return Sonar
     */
    protected function authenticate() {
        $sonarClient = new Sonar($this->sonarUrl, $this->sonarUser, $this->sonarPass);
        return $sonarClient;
    }
}