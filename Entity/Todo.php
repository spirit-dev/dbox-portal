<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TodoClass
 *
 * @ORM\Table(name="todo")
 * @ORM\Entity(repositoryClass="SpiritDev\Bundle\DBoxPortalBundle\Entity\TodoRepository")
 */
class Todo {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", nullable=true)
     */
    protected $content;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
}