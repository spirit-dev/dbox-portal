<?php

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