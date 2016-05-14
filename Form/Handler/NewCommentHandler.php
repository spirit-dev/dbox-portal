<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class NewCommentHandler
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Handler
 */
class NewCommentHandler {

    protected $request;
    protected $em;
    protected $form;
    protected $container;
    protected $id;

    /**
     * Contructor
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
     * Processing Comment handler
     * @param $id
     * @return null|object|Demand
     */
    public function process($id) {
        $issue = null;

        $this->id = $id;
        // If request is POST
        if ('POST' === $this->request->getMethod()) {
            // Register demand
            $issue = $this->registerComment();
        }
        return $issue;
    }

    /**
     * Register comment in DB
     * @return null|object|Demand
     */
    private function registerComment() {
        $demand = $this->em->getRepository('SpiritDevDBoxPortalBundle:Demand')->findOneBy(array('id' => $this->id));
        $demand->addComment($this->setCommentData($demand));
        $this->em->flush();
        return $demand;
    }

    /**
     * Set comment datas
     * @param $demand
     * @return Comment
     */
    private function setCommentData($demand) {
        $comment = new Comment();

        $comment->setContent($this->request->request->all()['demand_new_comment']['content']);
        $comment->setUser($this->getCurrentUser());

        $this->em->persist($comment);
        $this->em->flush();
        return $comment;
    }

    /**
     * Get current user session stored
     * @return mixed
     */
    private function getCurrentUser() {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

}