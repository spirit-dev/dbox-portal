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
 * File           DemandController.php
 * Updated the    25/05/16 10:55
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Comment;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class DemandController
 * @package SpiritDev\Bundle\DBoxPortalBundle\Controller
 */
class DemandController extends Controller {

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/demands", name="spirit_dev_dbox_portal_bundle_demands")
     * @Template()
     */
    public function demandsAction() {
        $user = $this->getCurrentUser();

        $demands = $this->getDoctrine()->getRepository('SpiritDevDBoxPortalBundle:Demand')->findBy(array('applicant' => $user));

        return array('demands' => $demands);
    }

    /**
     * @return mixed
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
     * @Route("/demand/{id}", name="spirit_dev_dbox_portal_bundle_demand")
     * @Template()
     * @param $id
     * @return array
     */
    public function demandAction($id) {
        $demand = $this->getDoctrine()->getRepository('SpiritDevDBoxPortalBundle:Demand')->findOneBy(array('id' => $id));

        return array('demand' => $demand);
    }

    /**
     * @param Request $request
     * @return array|RedirectResponse
     *
     * @Route("/register", name="spirit_dev_dbox_portal_bundle_demand_new_user")
     */
    public function demandNewUserAction(Request $request) {
        // Creating empty form
        $user = new User();
        $form = $this->createForm('demand_new_user', $user);

        // Populating form depending on request
        $form->handleRequest($request);

        // If form is valid and populated
        if ($form->isValid()) {
            // Registering Form
            $issue = $this->get('spirit_dev_dbox_portal_bundle.form.handler.demand_new_user')->process();

            if ($issue != null) {
                // Send mails
                $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
                $mailer->newUserSendMail($issue);

                // Flashbag
                $this->get('session')->getFlashBag()->set('success', 'flashbag.demand.new_user.success');
            } else {
                $this->get('session')->getFlashBag()->set('error', 'flashbag.demand.new_user.error');
            }
            return new RedirectResponse($this->getRedirectionUrl());
        }

        // Return generated form
        return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewUser.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Generate the redirection url when editing is completed.
     *
     * @return string
     */
    protected function getRedirectionUrl() {
        return $this->container->get('router')->generate('spirit_dev_dbox_portal_bundle_demands');
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param $demandId
     * @return string
     *
     * @Route("/newcomment/{demandId}", name="spirit_dev_dbox_portal_bundle_demand_add_comment")
     */
    public function demandAddCommentAction($demandId, Request $request) {
        // Creating empty form
        $newComment = new Comment();
        $form = $this->createForm('demand_new_comment', $newComment);

        // Populating form depending on request
        $form->handleRequest($request);

        // If form is valid and populated
        if ($form->isValid()) {
            // Registering comment
            $issue = $this->get('spirit_dev_dbox_portal_bundle.form.handler.demand_new_comment')->process($demandId);
            if ($issue != null) {
                // Send mails
                $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
                $mailer->newCommentSendMail($issue, $this->getCurrentUser());
                $this->get('session')->getFlashBag()->set('success', 'flashbag.demand.new_comment.success');
            } else {
                $this->get('session')->getFlashBag()->set('error', 'flashbag.demand.new_comment.error');
            }
            return new RedirectResponse($this->getRedirectionUrl());
        }

        // Return generated form
        return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewComment.html.twig', array(
            'form' => $form->createView(),
            'id' => $demandId
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/demands/newproject", name="spirit_dev_dbox_portal_bundle_demand_new_project")
     */
    public function demandNewProjectAction(Request $request) {
        // Creating empty form
        $newProject = new Project();
        $form = $this->createForm('demand_new_project', $newProject);

        // Populating form depending on request
        $form->handleRequest($request);

        // If form is valid and populated
        if ($form->isValid()) {

            if ($request->request->get('pmManage') == 'yes') {
                $newProject->setPmManaged(true);
            } else {
                $newProject->setPmManaged(false);
            }
            if ($request->request->get('vcsManage') == 'yes') {
                $newProject->setVcsManaged(true);
            } else {
                $newProject->setVcsManaged(false);
            }

            // Registering Form
            $issue = $this->get('spirit_dev_dbox_portal_bundle.form.handler.demand_new_project')->process($newProject);

            if ($issue != null) {

                // Send mails
                $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
                $mailer->projectRequestSendMail($issue, $newProject);

                // Flashbag
                $this->get('session')->getFlashBag()->set('success', 'flashbag.demand.new_project.success');
            } else {
                $this->get('session')->getFlashBag()->set('error', 'flashbag.demand.new_project.error');
            }
            return new RedirectResponse($this->getRedirectionUrl());
        }

        // Return generated form
        return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewProject.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/demands/newpipeline", name="spirit_dev_dbox_portal_bundle_demand_new_pipeline")
     */
    public function demandNewPipelineAction(Request $request) {

        $user = $this->getCurrentUser();
        $em = $this->getDoctrine()->getManager();
        $userProjects = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findBy(array('owner' => $user));
        // If current user hasn't owned projects
        if (!$userProjects || count($userProjects) == 0) {
            return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewPipeline.html.twig', array(
                'error' => true
            ));
        }

        // If current user, process form normally
        $form = $this->createForm('demand_new_pipeline');
        // Populating form depending on request
        $form->handleRequest($request);

        // If form is valid and populated
        if ($form->isValid()) {

            // Registering Form
            $issue = $this->get('spirit_dev_dbox_portal_bundle.form.handler.new_pipeline')->process();

            if ($issue != null) {

                // Send mail to admin and manager
                $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
                $mailer->newPipelineRequestSendMail($issue, $request->request->get('project'));

                // Flashbag
                $this->get('session')->getFlashBag()->set('success', 'flashbag.demand.new_pipeline.success');
            } else {
                $this->get('session')->getFlashBag()->set('error', 'flashbag.demand.new_pipeline.error');
            }
            return new RedirectResponse($this->getRedirectionUrl());
        }

        // Return generated form
        return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewPipeline.html.twig', array(
            'form' => $form->createView(),
            'userProjects' => $userProjects
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @Route("/demands/newsecurity", name="spirit_dev_dbox_portal_bundle_demand_new_security")
     */
    public function demandNewSecurityAction(Request $request) {

        $user = $this->getCurrentUser();
        $em = $this->getDoctrine()->getManager();
        $userProjects = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findBy(array('owner' => $user, 'ciDevManaged' => true));
        // If current user hasn't owned projects
        if (!$userProjects || count($userProjects) == 0) {
            return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewSecurity.html.twig', array(
                'error' => true
            ));
        }

        // If current user, process form normally
        $form = $this->createForm('demand_new_security');
        // Populating form depending on request
        $form->handleRequest($request);

        // If form is valid and populated
        if ($form->isValid()) {

            // Registering Form
            $issue = $this->get('spirit_dev_dbox_portal_bundle.form.handler.new_security')->process();

            if ($issue != null) {

                // Send mail to admin and manager
                $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
                // Send mail
                $mailer->newSecurityRequestSendMail($issue, $request->request->get('project'));

                // Flashbag
                $this->get('session')->getFlashBag()->set('success', 'flashbag.demand.new_security.success');
            } else {
                $this->get('session')->getFlashBag()->set('error', 'flashbag.demand.new_security.error');
            }
            return new RedirectResponse($this->getRedirectionUrl());
        }

        // Return generated form
        return $this->render('SpiritDevDBoxPortalBundle:Demand/Form:demandNewSecurity.html.twig', array(
            'form' => $form->createView(),
            'userProjects' => $userProjects
        ));
    }
}
