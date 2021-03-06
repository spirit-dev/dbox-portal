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
 * File           DefaultController.php
 * Updated the    06/06/16 17:46
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DefaultController
 * @package SpiritDev\Bundle\DBoxPortalBundle\Controller
 */
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
            $allProject = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findBy(array('active' => true));
        } else {
            // Get current user projects where he is member
            $userMemberProjects = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->getProjectsByTeamMember($currentUser);
            $userOwnedProjects = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->getProjectsByOwner($currentUser);
        }


        // Get some métrics
        $nbProjects = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->getNbActiveProjects();

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

    /**
     * @Route("/status/{app}", name="spirit_dev_dbox_portal_bundle_status")
     * @param null $app
     * @return JsonResponse
     */
    public function serverStatusAction($app = null) {
        // Set default vars
        $format = 'Y-m-d H:i:s';
        $returnValues = array();
        $dateStart = new \DateTime();

        // Calling each API
        if ($app == null || $app == 'ci') {
            $returnValues['bool']['ci'] = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins')->isAvailable();
            $returnValues['int']['ci'] = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins')->isAvailable() ? 1 : 0;
        }
        if ($app == null || $app == 'vcs') {
            $returnValues['bool']['vcs'] = $this->get('spirit_dev_dbox_portal_bundle.api.gitlab')->isAvailable();
            $returnValues['int']['vcs'] = $this->get('spirit_dev_dbox_portal_bundle.api.gitlab')->isAvailable() ? 1 : 0;
        }
        if ($app == null || $app == 'pm') {
            $returnValues['bool']['pm'] = $this->get('spirit_dev_dbox_portal_bundle.api.redmine')->isAvailable();
            $returnValues['int']['pm'] = $this->get('spirit_dev_dbox_portal_bundle.api.redmine')->isAvailable() ? 1 : 0;
        }
        if ($app == null || $app == 'qa') {
            $returnValues['bool']['qa'] = $this->get('spirit_dev_dbox_portal_bundle.api.sonar')->isAvailable();
            $returnValues['int']['qa'] = $this->get('spirit_dev_dbox_portal_bundle.api.sonar')->isAvailable() ? 1 : 0;
        }
        if ($app == null || $app == 'portal') {
            $returnValues['bool']['portal'] = true;
            $returnValues['int']['portal'] = 1;
        }

        $dateEnd = new \DateTime();

        return new JsonResponse(array(
            'servers_availability' => array(
                'bool' => $returnValues['bool'],
                'int' => $returnValues['int'],
            ),
            'requests' => array(
                'start' => $dateStart->format($format),
                'end' => $dateEnd->format($format),
                'duration' => $dateStart->diff($dateEnd)
            )
        ));
    }
}
