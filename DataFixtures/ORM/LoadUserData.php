<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Class LoadUserData
 * @package SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM
 */
class LoadUserData implements FixtureInterface, ContainerAwareInterface {

    private $container;
    private $encoder;

    /**
     * initializing container
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
        $this->encoder = $this->container->get('security.password_encoder');
    }

    /**
     * Loading User datas
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        // Creating Super Admin User
        $userSuper = new User();
        $userSuper->setUsername('sys');
        $password = $this->encoder->encodePassword($userSuper, 'sys');
        $userSuper->setPassword($password);
        $userSuper->setEmail('sys@ecosystem.v2');
        $userSuper->setDn('uid=sys,dc=ldap,dc=test');
        $userSuper->setLastName('sys');
        $userSuper->setFirstName('sys');
        $userSuper->setLanguage('en');
        $userSuper->setEnabled(true);
        $userSuper->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($userSuper);

        // Creating Admin user
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $password = $this->encoder->encodePassword($userAdmin, 'admin');
        $userAdmin->setPassword($password);
        $userAdmin->setEmail('admin@ecosystem.v2');
        $userAdmin->setDn('uid=admin,dc=ldap,dc=test');
        $userAdmin->setLastName('admin');
        $userAdmin->setFirstName('admin');
        $userAdmin->setLanguage('en');
        $userAdmin->setEnabled(true);
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $manager->persist($userAdmin);

        // Flushing datas
        $manager->flush();
    }
}