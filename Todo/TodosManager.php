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
 * File           TodosManager.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Todo;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Todo;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TodosManager
 * @package SpiritDev\Bundle\DBoxPortalBundle\TodoManager
 */
class TodosManager {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * TodosManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $content
     * @return TodoEntity
     */
    public function addTodo($content) {

        $em = $this->container->get('doctrine')->getEntityManager();

        $todo = new Todo();
        $todo->setContent($content);

        $em->persist($todo);
        $em->flush();

        return $todo;
    }

    /**
     * @param $id
     * @return null|object|TodoEntity
     */
    public function deleteTodo($id) {
        $em = $this->container->get('doctrine')->getEntityManager();

        $todo = $em->getRepository('SpiritDevDBoxPortalBundle:Todo')->findOneBy(array('id' => $id));

        $em->remove($todo);
        $em->flush();

        return $todo;
    }

}