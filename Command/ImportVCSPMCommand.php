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
 * File           ImportVCSPMCommand.php
 * Updated the    12/06/16 20:34
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ImportCVSPMCommand
 * @package SpiritDev\Bundle\DBoxPortalBundle\Command
 */
class ImportVCSPMCommand extends ContainerAwareCommand {

    /**
     *
     */
    protected function configure() {
        $this
            ->setName('dbox:portal:pmvcs:check')
            ->setDescription('Checks projects from remote existing PM and VCS')
//            ->setDefinition(array(
//                new InputArgument('username', InputArgument::REQUIRED, 'Username'),
//            ))
            ->setHelp(<<<EOT
The <info>dbox:portal:pmvcs:check</info> checks existing projects from PM and VCS:

  <info>php app/console dbox:portal:pmvcs:check</info>
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        // Important vars
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $io = new SymfonyStyle($input, $output);
        // API's
        $apiPM = $container->get('spirit_dev_dbox_portal_bundle.api.redmine');
        $apiVCS = $container->get('spirit_dev_dbox_portal_bundle.api.gitlab');

        // Get PM Projects
        $pmProjects = $apiPM->listProjects();
        if ($pmProjects["total_count"] > $pmProjects["limit"]) {
            $pmProjects = $apiPM->listProjects(array("limit" => $pmProjects["total_count"]));
        }
        $pmProjects = $pmProjects["projects"];
        // Get VCS Projects
        $vcsProjects = $apiVCS->listProjects(1, 10000);
        // Get local projects
        $dbProjects = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findAll();
        // Merge all arrays
        $allProjects = array();
        foreach ($pmProjects as $pm) $allProjects[] = array('name' => $pm['name'], 'type' => 'pm', 'id' => $pm['id']);
        foreach ($vcsProjects as $vcs) $allProjects[] = array('name' => $vcs['name'], 'type' => 'vcs', 'id' => $vcs['id']);
        foreach ($dbProjects as $db) $allProjects[] = array('name' => $db->getName(), 'type' => 'db', 'id' => $db->getId());

        // Shake and shake and shake
        // Fix PM projects (public, modules)
        $pmOnly = array();
        $vsnOnly = array();
        $dbOnly = array();

        $io->title('CHECKING CORRESPONDENCES');
        $io->section('Global Checkup');
        $globalTableRow = array();

        foreach ($allProjects as $project) {
            $pmPresent = false;
            $vcsPresent = false;
            $dbPresent = false;

            foreach ($pmProjects as $pm) {
                if (strtolower($project['name']) == strtolower($pm['name'])) {
                    $pmPresent = true;
                }
            }
            foreach ($vcsProjects as $vcs) {
                if (strtolower($project['name']) == strtolower($vcs['name'])) {
                    $vcsPresent = true;
                }
            }
            foreach ($dbProjects as $db) {
                if (strtolower($project['name']) == strtolower($db->getName())) {
                    $dbPresent = true;
                }
            }
            $globalTableRow[] = array($project['name'], $project['type'], $project['id'], $pmPresent, $vcsPresent, $dbPresent);
        }
        $io->table(
            array('Project', 'Type', 'Id', 'PM Present', 'VCS Present', 'DB Present'),
            $globalTableRow
        );

        $io->section('Focused Checkup');
        $tmpFocusedTableRow = array();
        foreach ($globalTableRow as $globalRow) {
            $alreadyExists = false;
            foreach ($tmpFocusedTableRow as $focusRow) {
                if (strtolower($focusRow[0]) == strtolower($globalRow[0])) $alreadyExists = true;
            }
            if (!$alreadyExists && $globalRow[3] && $globalRow[4]) $tmpFocusedTableRow[] = array($globalRow[0], $globalRow[3], $globalRow[4], $globalRow[5]);
        }

        $focusedTableRow = array();
        foreach ($tmpFocusedTableRow as $trow) {
            $pmId = null;
            $pmName = null;
            $vcsId = null;
            $vcsName = null;
            foreach ($globalTableRow as $grow) {
                if (strtolower($grow[0]) == strtolower($trow[0])) {
                    if ($grow[1] == "pm") $pmId = $grow[2];
                    $pmName = $grow[0];
                    if ($grow[1] == "vcs") $vcsId = $grow[2];
                    $vcsName = $grow[0];
                }
            }
            $focusedTableRow[] = array($trow[0], $trow[1], $pmName, $pmId, $trow[2], $vcsId, $vcsName, $trow[3]);
        }

        $io->table(
            array('Project', 'PM Present', 'PM ID', 'PM Name', 'VCS Present', 'VCS ID', 'VCS Name', 'DB Present'),
            $focusedTableRow
        );

    }

}