<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class DeleteUserCommand
 * @package SpiritDev\Bundle\DBoxPortalBundle\Command
 */
class JenkinsTestCommand extends ContainerAwareCommand {

    /**
     *
     */
    protected function configure() {
        $this
            ->setName('portal:jenkins:test')
            ->setDescription('Test for jenkins config')
//            ->setDefinition(array(
//                new InputArgument('username', InputArgument::REQUIRED, 'Username'),
//            ))
            ->setHelp(<<<EOT
The <info>portal:jenkins:test</info> tests jenkins configuration via xml file:

  <info>php app/console portal:jenkins:test</info>
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $pjt = $this->getContainer()->get('doctrine')->getRepository('SpiritDevBundleDBoxPortalBundle:Project')->find(3);
//        $pjtd = $this->getContainer()->get('doctrine')->getRepository('SpiritDevBundleDBoxPortalBundle:Project')->find(4);

//        $gitRepoName = substr($pjt->getGitLabSshUrlToRepo(), strrpos($pjt->getGitLabSshUrlToRepo(), '/'));
//        $gitPath = "/var/opt/gitlab/git-data/repositories/root".$gitRepoName;
//        $localPath = $this->getContainer()->get('kernel')->getRootDir().'/../web/gitClone';
//        $fs = new Filesystem();
//        try {
//            $fs->mkdir($localPath);
//        } catch (\Exception $e) {
//
//        }
//
//        $wrapper = new GitWrapper();
////        $wrapper->setPrivateKey($this->getContainer()->get('kernel')->getRootDir().'/../src/SpiritDev\Bundle\DBoxPortalBundle/Resources/ssh/id_rsa_portal');
//        $wrapper->git('config user.name "Administrator"');
//        $wrapper->git('config user.email master@ecosystemv2.com');
//        $git = $wrapper->clone($gitPath, $localPath);
//
////        $git = new \PHPGit\Git();
////        $git->clone($gitPath, $localPath);
////        $git->setRepository($localPath);
//
        $finder = new Finder();
        $finder->files()->in('src/SpiritDev\Bundle\DBoxPortalBundle/Resources/public/docs');
        foreach ($finder as $file) {
            // Dump the relative path to the file
            $fileName = $file->getRelativePathname();
            $fileContent = $file->getContents();
            $this->getContainer()->get('spirit_dev_dbox_portal_bundle.api.gitlab')->addFile($pjt, $fileName, base64_encode($fileContent), sprintf('Adds %s', $fileName), 'master', 'base64');
        }
//        $git
//            ->commit('Initial commit')
//            ->push();
//        dump($git);
//
//        try {
//            $fs->remove($localPath);
//        } catch(\Exception $e) {
//
//        }

//        $tree = $this->getContainer()->get('spirit_dev_dbox_portal_bundle.api.gitlab')->getTree($pjtd);
//
//        for ($i = 0; $i < count($tree); $i++) {
//            $fileArray = $tree[0];
//
//            $fileFromDefault = $this->getContainer()->get('spirit_dev_dbox_portal_bundle.api.gitlab')->getFile($pjtd, $fileArray['name']);
//            $fileContent = $fileFromDefault['content'];
//
//            $addedFile = $this->getContainer()->get('spirit_dev_dbox_portal_bundle.api.gitlab')->addFile($pjt, $fileArray['name'], $fileContent, sprintf('Adds %s', $fileArray['name']), 'master', 'base64');
//        }

        return null;
    }

}