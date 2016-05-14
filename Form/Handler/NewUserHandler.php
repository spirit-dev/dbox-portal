<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class NewUserHandler
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Handler
 */
class NewUserHandler {

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
     */
    private function registerDemand() {
        // Managing EM entities
        $status = $this->em->getRepository('SpiritDevDBoxPortalBundle:Status')->findOneBy(array('canonicalName' => 'new'));
        $type = $this->em->getRepository('SpiritDevDBoxPortalBundle:Type')->findOneBy(array('canonicalName' => 'new_user'));

        // Creating empty demand
        $demand = new Demand();
        // Setting datas
        $demand->setAskdate(new \DateTime());
//        $demand->setApplicant($this->getCurrentUser());
        // For new user registration, set applicant to null...
        $demand->setApplicant(null);
        $demand->setStatus($status);
        $demand->setType($type);
        $demand->setContent($this->setContentData());

        // Persisting EM entity
        $this->em->persist($demand);
        $this->em->flush();

        return $demand;
    }

    /**
     * Setting demand content
     * @return array
     */
    private function setContentData() {

        $requestParam = $this->request->request->all()['demand_new_user'];

        return [
            "user_mail" => $requestParam["email"],
            "firstname" => $requestParam["firstname"],
            "lastname" => $requestParam["lastname"]
        ];
    }

    /**
     * Getting current session user
     * @return mixed
     */
    private function getCurrentUser() {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

}