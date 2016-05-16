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
 * File           NewCommunicationHandler.php
 * Updated the    16/05/16 14:53
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Communication;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class NewCommunicationHandler
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Handler
 */
class NewCommunicationHandler {

    /**
     * @var
     */
    protected $request;
    /**
     * @var EntityManager
     */
    protected $em;
    /**
     * @var
     */
    protected $form;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     * @param RequestStack $request
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(RequestStack $request, EntityManager $em, ContainerInterface $container) {
        // Setting datas
        $this->request = $request->getCurrentRequest();
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Processing new user registration
     * @param Communication $communication
     * @return bool
     */
    public function process(Communication $communication) {
        $issue = null;
        // If request is POST
        if ('POST' === $this->request->getMethod()) {
            // Register demand
            $issue = $this->registerCommunication($communication);
        }
        return $issue;
    }

    /**
     * Process demand registration
     * @param Communication $communication
     * @return Demand
     */
    private function registerCommunication(Communication $communication) {

        // Persist Project
        $this->em->persist($communication);
        $this->em->flush();

        return $communication;
    }

}