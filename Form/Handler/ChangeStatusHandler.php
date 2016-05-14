<?php

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

    protected $request;
    protected $em;
    protected $form;
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
        $demand = $this->em->getRepository('SpiritDevBundleDBoxPortalBundle:Demand')->findOneBy(array('id' => $demandId));
        $newStatus = $this->em->getRepository('SpiritDevBundleDBoxPortalBundle:Status')->findOneBy(array('id' => $newStatusId));

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