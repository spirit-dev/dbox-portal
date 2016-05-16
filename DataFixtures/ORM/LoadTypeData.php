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
 * File           LoadTypeData.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Type;

/**
 * Class LoadTypeData
 * @package SpiritDev\Bundle\DBoxPortalBundle\DataFixtures\ORM
 */
class LoadTypeData implements FixtureInterface {

    /**
     * Load Types datas
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager) {

        // New user demand type
        $demandTypeNewUser = new Type();
        $demandTypeNewUser->setName('New user');
        $demandTypeNewUser->setCanonicalName('new_user');
        $demandTypeNewUser->setDescription('Demand corresponding to a new user creation');
        $manager->persist($demandTypeNewUser);

        // New project
        $demandTypeNewProject = new Type();
        $demandTypeNewProject->setName('New project');
        $demandTypeNewProject->setCanonicalName('new_project');
        $demandTypeNewProject->setDescription('Demand corresponding to a new project creation');
        $manager->persist($demandTypeNewProject);

        // User modification
        $demandTypeModUser = new Type();
        $demandTypeModUser->setName('Modify user');
        $demandTypeModUser->setCanonicalName('modify_user');
        $demandTypeModUser->setDescription('Demand corresponding to a user modification');
        $manager->persist($demandTypeModUser);

        // Project modification
        $demandTypeModProject = new Type();
        $demandTypeModProject->setName('Modify project');
        $demandTypeModProject->setCanonicalName('modify_project');
        $demandTypeModProject->setDescription('Demand corresponding to a project modification');
        $manager->persist($demandTypeModProject);

        // Project modification
        $demandTypeNewPipeline = new Type();
        $demandTypeNewPipeline->setName('New pipeline');
        $demandTypeNewPipeline->setCanonicalName('new_pipeline');
        $demandTypeNewPipeline->setDescription('Demand corresponding to a CI new pipeline creation');
        $manager->persist($demandTypeNewPipeline);

        // Project modification
        $demandTypeNewSecurity = new Type();
        $demandTypeNewSecurity->setName('Security improvement');
        $demandTypeNewSecurity->setCanonicalName('new_security');
        $demandTypeNewSecurity->setDescription('Demand corresponding to a security improvement creation');
        $manager->persist($demandTypeNewSecurity);

        // Persist objects
        $manager->flush();
    }
}