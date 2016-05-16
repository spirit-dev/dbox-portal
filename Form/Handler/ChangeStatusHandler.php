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
 * File           ChangeStatusHandler.php
 * Updated the    16/05/16 14:53
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ChangeStatusHandler
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Handler
 */
class ChangeStatusHandler {

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
        $this->request = $request->getCurrentRequest();
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Processing status change
     * @param $demandId
     * @return null|void
     */
    public function process($demandId) {
        $issue = null;
        if ('POST' === $this->request->getMethod()) {
            // Register demand
            $issue = $this->updateDemand($demandId);
        }
        return $issue;
    }

    /**
     * Updating demand
     * @param $demandId
     * @return null|object|Demand
     */
    private function updateDemand($demandId) {
        // Getting params
        $requestParam = $this->request->request->all()['demand_change_status'];
        $newStatusId = $requestParam["status"];
        // Managing EM entities
        $demand = $this->em->getRepository('SpiritDevDBoxPortalBundle:Demand')->findOneBy(array('id' => $demandId));
        $newStatus = $this->em->getRepository('SpiritDevDBoxPortalBundle:Status')->findOneBy(array('id' => $newStatusId));

        // Setting new values
        $demand->setStatus($newStatus);
        // Updating resolution date depending on status
        if ($newStatus->getCanonicalName() == "resolved") {
            $demand->setResolutionDate(new \DateTime());
        } else {
            $demand->setResolutionDate(null);
        }

        // Updating via EM
        $this->em->flush();

        return $demand;
    }
}