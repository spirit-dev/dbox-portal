<?php

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