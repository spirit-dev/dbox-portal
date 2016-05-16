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
 * File           SonarAPICore.php
 * Updated the    16/05/16 12:28
 */

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

        $this->sonarUrl = $container->getParameter('spirit_dev_d_box_portal.sonar_api.url');
        $this->sonarUser = $container->getParameter('spirit_dev_d_box_portal.sonar_api.username');
        $this->sonarPass = $container->getParameter('spirit_dev_d_box_portal.sonar_api.password');

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