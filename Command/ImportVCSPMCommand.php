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
 * Updated the    14/06/16 20:42
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Command;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
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
            ->setName('dbox:portal:pmvcs:import')
            ->setDescription('Import projects from remote existing PM and VCS projects')
            ->setDefinition(array(
                new InputArgument('from', InputArgument::REQUIRED, 'Import from (pm|vcs|both)'),
                new InputArgument('pjtname', InputArgument::OPTIONAL, 'Project Name'),
                new InputOption('force-update', false, InputOption::VALUE_NONE, 'Force the local entity update.'),
                new InputOption('force', false, InputOption::VALUE_NONE, 'Force the local entity creation.')
            ))
            ->setHelp(<<<EOF
The <info>dbox:portal:pmvcs:import</info> checks existing projects from PM and VCS:

  <info>php app/console dbox:portal:pmvcs:check</info>
EOF
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        // Get inputs
        $from = $input->getArgument('from');
        $pjtname = $input->getArgument('pjtname');
        // Important vars
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $io = new SymfonyStyle($input, $output);
//        // API's
        $apiPM = $container->get('spirit_dev_dbox_portal_bundle.api.redmine');
        $apiVCS = $container->get('spirit_dev_dbox_portal_bundle.api.gitlab');

        $io->title(sprintf('Import project     -- %s --     from     -- %s --', $pjtname, $from));

        $pmProject = null;
        $vcsProject = null;
        $dbProject = null;

        // Import values from PM if requested
        if ($from == 'pm' || $from == 'both') {
            $io->section('PM Check');
            $pmProjectId = $apiPM->getProjectIdByName($pjtname);
            if (intval($pmProjectId)) {
                $tmpPjt = new Project();
                $tmpPjt->setRedmineProjectId($pmProjectId);
                $pmProject = $apiPM->showProject($tmpPjt);
                $io->success('Project -- ' . $pjtname . ' -- found in PM');
                $io->table(array('Name', 'Id', 'Identifier', 'description'), array(array(
                    $pmProject['project']['name'],
                    $pmProject['project']['id'],
                    $pmProject['project']['identifier'],
                    empty($pmProject['project']['description']) ? "none" : $pmProject['project']['description']
                )));
            } else {
                $io->error('This project does not exists in PM, check value case');
                if ($from != "both") exit(0);
            }
        }

        // Import values from VCS if requested
        if ($from == 'vcs' || $from == 'both') {
            $io->section('VCS Check');
            $vcsProjectId = $apiVCS->getProjectIdByName($pjtname);
            if (intval($vcsProjectId)) {
                $vcsProject = $apiVCS->getProject($vcsProjectId);
                $io->success('Project -- ' . $pjtname . ' -- found in VCS');
                $io->table(array('Name', 'Id', 'description', 'SSH Url', 'HTTP Url', 'WEB Url', 'Namespace', 'Issues', 'Wiki', 'Snippets'), array(array(
                    $vcsProject['name'],
                    $vcsProject['id'],
                    $vcsProject['description'],
                    $vcsProject['ssh_url_to_repo'],
                    $vcsProject['http_url_to_repo'],
                    $vcsProject['web_url'],
                    $vcsProject['path_with_namespace'],
                    $vcsProject['issues_enabled'],
                    $vcsProject['wiki_enabled'],
                    $vcsProject['snippets_enabled']
                )));
            } else {
                $io->error('This project does not exists in VCS, check value case');
                exit(0);
            }
        }

        // Check if project exists in db
        $io->section('DB Check');
        $dbProject = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array(
            'canonicalName' => strtolower($pjtname)
        ));
        if ($dbProject) {
            $io->note('Project -- ' . $pjtname . ' -- already exists in database!');
            $io->caution('Local DB Project will be update');
        } else {
            $io->note('Project -- ' . $pjtname . ' -- does not exists in database!');
            $io->note('A local DB Project will be created');
        }

        // Starting import
        $io->section('Starting Importation');
        if ($dbProject) {
            // Updating entity
            if ($input->getOption('force-update') && $io->confirm('Do you want to continue ?', true)) {
                // Import PM Values
                if ($from == "pm" || $from == "both") {
                    $io->writeln('Importing from PM');
                    $dbProject->setRedmineProjectId($pmProject['project']['id']);
                    $dbProject->setRedmineProjectIdentifier($pmProject['project']['identifier']);
                    $dbProject->setRedmineWebUrl($apiPM->getProjectWebUrl($dbProject));

                    $io->writeln('Updating PM remote project');
                    $dbProject->setName($pjtname);
                    $update = $apiPM->updateProject($dbProject);
                    dump($update);
                }
                // Import VCS Values
                if ($from == "vcs" || $from == "both") {
                    $io->writeln('Importing from VCS');
                    $dbProject->setGitLabIssueEnabled($vcsProject['issues_enabled']);
                    $dbProject->setGitLabWikiEnabled($vcsProject['wiki_enabled']);
                    $dbProject->setGitLabSnippetsEnabled($vcsProject['snippets_enabled']);
                    $dbProject->setGitLabProjectId($vcsProject['id']);
                    $dbProject->setGitLabSshUrlToRepo($vcsProject['ssh_url_to_repo']);
                    $dbProject->setGitLabHttpUrlToRepo($vcsProject['http_url_to_repo']);
                    $dbProject->setGitLabWebUrl($vcsProject['web_url']);
                    $dbProject->setGitLabNamespace($vcsProject['path_with_namespace']);

//                    $io->writeln('Updating VCS remote project');
                }
                $io->writeln('Updating entity');
                $dbProject->setName($pjtname);
                $dbProject->setActive(true);
//                $em->persist($dbProject);
                $em->flush();
                $io->success('Done');
            } else {
                $io->error('You may reload the command with the --force-update option');
                $io->note('php app/console dbox:portal:pmvcs:import ' . $from . ' ' . $pjtname . ' --force-update');
                exit(0);
            }
        } else {
            // Creating entity
            if ($input->getOption('force') && $io->confirm('Do you want to continue ?', true)) {
                $project = new Project();
                // Import PM Values
                if ($from == "pm" || $from == "both") {
                    $io->writeln('Importing from PM');
                    $project->setRedmineProjectId($pmProject['project']['id']);
                    $project->setRedmineProjectIdentifier($pmProject['project']['identifier']);
                    $project->setRedmineWebUrl($apiPM->getProjectWebUrl($project));

                    $io->writeln('Updating PM remote project');
                    $project->setName($pjtname);
                    $update = $apiPM->updateProject($project);
                    dump($update);
                }
                // Import VCS Values
                if ($from == "vcs" || $from == "both") {
                    $io->writeln('Importing from VCS');
                    $project->setGitLabIssueEnabled($vcsProject['issues_enabled']);
                    $project->setGitLabWikiEnabled($vcsProject['wiki_enabled']);
                    $project->setGitLabSnippetsEnabled($vcsProject['snippets_enabled']);
                    $project->setGitLabProjectId($vcsProject['id']);
                    $project->setGitLabSshUrlToRepo($vcsProject['ssh_url_to_repo']);
                    $project->setGitLabHttpUrlToRepo($vcsProject['http_url_to_repo']);
                    $project->setGitLabWebUrl($vcsProject['web_url']);
                    $project->setGitLabNamespace($vcsProject['path_with_namespace']);

//                    $io->writeln('Updating VCS remote project');
                }
                $io->writeln('Creating entity');
                $project->setName($pjtname);
                $project->setActive(true);
                $em->persist($project);
                $em->flush();
                $io->success('Done');
            } else {
                $io->error('You may reload the command with the --force option');
                $io->note('php app/console dbox:portal:pmvcs:import ' . $from . ' ' . $pjtname . ' --force');
                exit(0);
            }
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output) {

        // Checking "FROM" value
        $availableValues = ['pm', 'vcs', 'both'];
        $testFrom = $input->getArgument('from');
        if (!$testFrom || !in_array($testFrom, $availableValues)) {
            $from = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Import a project from (pm|vcs|both): ',
                function ($from) {
                    if (empty($from)) {
                        throw new \Exception('Import from must not be empty');
                    }
                    $availableValues = ['pm', 'vcs', 'both'];
                    if (!in_array($from, $availableValues)) {
                        throw new \Exception('Import from must be one of ("pm"|"vcs"|"both")');
                    }

                    return $from;
                }
            );
            $input->setArgument('from', $from);
        }

        // Checking "PJTNAME" value
        if (!$input->getArgument('pjtname')) {
            $pjtname = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Choose a project name to import: ',
                function ($pjtname) {
                    if (empty($pjtname)) {
                        throw new \Exception('Import from must not be empty');
                    }

                    return $pjtname;
                }
            );
            $input->setArgument('pjtname', $pjtname);
        }

    }

}