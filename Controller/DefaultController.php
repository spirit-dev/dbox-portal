<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class DefaultController extends Controller {

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/", name="spirit_dev_dbox_portal_bundle_homepage")
     * @Template()
     */
    public function indexAction() {

        $em = $this->getDoctrine()->getEntityManager();
        $currentUser = $this->getCurrentUser();

        $allProject = null;
        $userMemberProjects = null;
        $userOwnedProjects = null;

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            // Get All projects if user is admin
            $allProject = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Project')->findBy(array('active' => true));
        } else {
            // Get current user projects where he is member
            $userMemberProjects = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Project')->getProjectsByTeamMember($currentUser);
            $userOwnedProjects = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Project')->getProjectsByOwner($currentUser);
        }


        // Get some métrics
        $nbProjects = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Project')->getNbActiveProjects();

        return array(
            'all_projects' => $allProject,
            'member_projects' => $userMemberProjects,
            'owner_projects' => $userOwnedProjects,
            'nb_projects' => $nbProjects
        );
    }

    /**
     * @return mixed
     * @throws AccessDeniedException
     */
    protected function getCurrentUser() {

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $user;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/servers/available/{scope}", name="spirit_dev_dbox_portal_bundle_applications_available")
     * @Template()
     * @param $scope
     * @return mixed
     */
    public function serverAvailabilityAction($scope) {

        $servArray['scope'] = $scope;

        // Calling each API
        $servArray['ci'] = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins')->isAvailable();
        $servArray['vcs'] = $this->get('spirit_dev_dbox_portal_bundle.api.gitlab')->isAvailable();
        $servArray['pm'] = $this->get('spirit_dev_dbox_portal_bundle.api.redmine')->isAvailable();
        $servArray['qa'] = $this->get('spirit_dev_dbox_portal_bundle.api.sonar')->isAvailable();

        return $servArray;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/intro", name="spirit_dev_dbox_portal_bundle_introduction")
     * @Template()
     * @return array
     */
    public function introductionAction() {

        $user = $this->getCurrentUser();

        // Redirect auto for admins
        // Redirect for users who doesn't want to see intro
        if ($user->getUsername() == 'admin' || $user->getUsername() == 'sys' || $user->getSkipIntro() == true) {
            return $this->redirectToRoute('spirit_dev_dbox_portal_bundle_homepage');
        }

        return array('no_server_check' => true);
    }
}
