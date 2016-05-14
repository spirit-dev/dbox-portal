<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Status;

/**
 * Class LoadStatusData
 * @package SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM
 */
class LoadStatusData implements FixtureInterface {

    /**
     * Load Status Datas
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        // New - Grey
        $newStatus = new Status();
        $newStatus->setName('New');
        $newStatus->setCanonicalName('new');
        $newStatus->setDescription('Demand just created');
        $newStatus->setColor("#777777");
        $manager->persist($newStatus);

        // Processing - Blue
        $processingStatus = new Status();
        $processingStatus->setName('Processing');
        $processingStatus->setCanonicalName('processing');
        $processingStatus->setDescription('Demand is being processed');
        $processingStatus->setColor('#337ab7');
        $manager->persist($processingStatus);

        // Cancelled - Red
        $cancelledStatus = new Status();
        $cancelledStatus->setName('Cancelled');
        $cancelledStatus->setCanonicalName('cancelled');
        $cancelledStatus->setDescription('Demand cancelled');
        $cancelledStatus->setColor('#d9534f');
        $manager->persist($cancelledStatus);

        // Resolved - Green
        $resolvedStatus = new Status();
        $resolvedStatus->setName('Resolved');
        $resolvedStatus->setCanonicalName('resolved');
        $resolvedStatus->setDescription('Demand resoled');
        $resolvedStatus->setColor('#5cb85c');
        $manager->persist($resolvedStatus);

        // Problem - Orange
        $problemStatus = new Status();
        $problemStatus->setName('Problem');
        $problemStatus->setCanonicalName('problem');
        $problemStatus->setDescription('Demand is detecting a problem');
        $problemStatus->setColor('#f0ad4e');
        $manager->persist($problemStatus);

        // Persist objects
        $manager->flush();
    }
}