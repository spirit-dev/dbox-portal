<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeleteUserCommand
 * @package SpiritDev\Bundle\DBoxPortalBundle\Command
 */
class DeleteUserCommand extends ContainerAwareCommand {

    /**
     *
     */
    protected function configure() {
        $this
            ->setName('portal:user:remove')
            ->setDescription('Remove user from local and distant apps')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Username'),
            ))
            ->setHelp(<<<EOT
The <info>portal:user:remove</info> command removes a user from local DB and from VCS-PM-CI-QA applications:

  <info>php app/console portal:user:remove jdoe</info>
EOT
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        // Get input
        $username = $input->getArgument('username');

        // Getting DB Datas
        $em = $this->getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository('UserBundle:User')->findOneBy(array('username' => $username));

        if ($user) {

            $ret = $this->getContainer()->get('spirit_dev_dbox_portal_bundle.admin.delete_user_process')->deleteUser($user);

            $output->writeln(sprintf('User "%s" has been removed from following :', $username));

            $output->writeln(sprintf('    - Database : %s', $ret['dbIssue'] ? 'true' : 'false'));
            $output->writeln(sprintf('    - LDAP     : %s', $ret['ldapIssue'] ? 'true' : 'false'));
            $output->writeln(sprintf('    - VCS      : %s', $ret['vcsIssue'] ? 'true' : 'false'));
            $output->writeln(sprintf('    - PM       : %s', $ret['pmIssue'] ? 'true' : 'false'));
            $output->writeln(sprintf('    - CI       : %s', $ret['ciIssue'] ? 'true' : 'false'));
            $output->writeln(sprintf('    - QA       : %s', $ret['qaIssue'] ? 'true' : 'false'));
        } else {
            $output->writeln(sprintf('User "%s" does not exist', $username));
        }

        return null;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output) {

        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username: ',
                function ($username) {
                    if (empty($username)) {
                        throw new \Exception('Username cannot be empty');
                    }

                    return $username;
                }
            );
            $input->setArgument('username', $username);
        }

    }

}