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
 * File           LoadStatusData.php
 * Updated the    15/05/16 11:47
 */

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