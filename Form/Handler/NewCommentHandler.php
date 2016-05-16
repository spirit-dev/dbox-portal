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
 * File           NewCommentHandler.php
 * Updated the    16/05/16 14:53
 */

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
     * @var
     */
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