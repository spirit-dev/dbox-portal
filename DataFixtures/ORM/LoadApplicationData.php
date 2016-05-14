<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Application;

/**
 * Class LoadApplicationData
 * @package SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM
 */
class LoadApplicationData implements FixtureInterface {

    /**
     * Load Status Datas
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        // Redmine
        $redmine = new Application();
        $redmine->setName('Redmine - PM');
        $redmine->setCanonicalName('redmine');
        $redmine->setCaptionUri('http://portal.devtest.fr/images/REDMINE_logo.png');
        $redmine->setUrl('http://redmine.devtest.fr/');
        $redmine->setOrder('1');
        $manager->persist($redmine);

        // Gitlab
        $gitlab = new Application();
        $gitlab->setName('GitLab - VCS');
        $gitlab->setCanonicalName('gitlab');
        $gitlab->setCaptionUri('http://portal.devtest.fr/images/GITLAB_logo.png');
        $gitlab->setUrl('http://git.devtest.fr/');
        $gitlab->setOrder(2);
        $manager->persist($gitlab);

        // Sonar
        $sonar = new Application();
        $sonar->setName('Sonar - QA');
        $sonar->setCanonicalName('sonar');
        $sonar->setCaptionUri('http://portal.devtest.fr/images/SONAR_logo.png');
        $sonar->setUrl('http://sonar.devtest.fr/');
        $sonar->setOrder(3);
        $manager->persist($sonar);

        // Jenkins
        $jenkins = new Application();
        $jenkins->setName('Jenkins - CI');
        $jenkins->setCanonicalName('jenkins');
        $jenkins->setCaptionUri('http://portal.devtest.fr/images/JENKINS_logo.png');
        $jenkins->setUrl('http://jenkins.devtest.fr/');
        $jenkins->setOrder(4);
        $manager->persist($jenkins);

        // Persist objects
        $manager->flush();
    }
}