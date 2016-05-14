<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class NewSecurityHandler
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Handler
 */
class NewSecurityHandler {

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
     * @return bool
     */
    public function process() {
        $issue = null;
        // If request is POST
        if ('POST' === $this->request->getMethod()) {
            // Register demand
            $issue = $this->registerDemand();
        }
        return $issue;
    }

    /**
     * Process demand registration
     * @return Demand
     */
    private function registerDemand() {

        // Managing EM entities
        $status = $this->em->getRepository('SpiritDevBundleDBoxPortalBundle:Status')->findOneBy(array('canonicalName' => 'new'));
        $type = $this->em->getRepository('SpiritDevBundleDBoxPortalBundle:Type')->findOneBy(array('canonicalName' => 'new_security'));

        // Creating empty demand
        $demand = new Demand();
        // Setting datas
        $demand->setAskdate(new \DateTime());
        $demand->setApplicant($this->getCurrentUser());
        $demand->setStatus($status);
        $demand->setType($type);
        $demand->setContent($this->setContentData());

        // Persisting EM entity
        $this->em->persist($demand);
        $this->em->flush();

        return $demand;
    }

    /**
     * Getting current session user
     * @return mixed
     */
    private function getCurrentUser() {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * Setting demand content
     * @return array
     */
    private function setContentData() {

        return [
            'project' => $this->request->request->get('project'),
            'requestor_ticket' => $this->request->request->get('demand_new_security')["ticketId"],
            'security_type' => $this->request->request->get('securityAnalysis'),
            'server_target' => $this->request->request->get('demand_new_security')["serverTarget"],
            'more_info' => $this->request->request->get('demand_new_security')["moreInfo"]
        ];
    }

}