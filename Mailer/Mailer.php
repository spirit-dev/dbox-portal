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
 * File           Mailer.php
 * Updated the    07/06/16 17:51
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Mailer;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Class Mailer
 * @package SpiritDev\Bundle\DBoxPortalBundle\Mailer
 */
class Mailer extends MailerCore implements MailerCoreInterface {

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param RouterInterface $router
     * @param EngineInterface $templating
     * @param ContainerInterface $container
     */
    public function __construct(\Swift_Mailer $mailer, RouterInterface $router, EngineInterface $templating, ContainerInterface $container) {
        parent::__construct($mailer, $router, $templating, $container);
    }

    /**
     * New user registration mailling
     *  To User
     *  To Admin
     * @param Demand $demand
     */
    public function newUserSendMail(Demand $demand) {

        // Send user mail
        // Define necessary vars
        $template = 'SpiritDevDBoxPortalBundle:Mailer/Registration:userRegistration.html.twig'; // Template
        $subject = $this->getSubject('User registration'); // Subject
        $rendered = $this->templating->render($template, array(
            'firstname' => $demand->getContent()['firstname'],
            'lastname' => $demand->getContent()['lastname']
        )); // Template rendering
        $userMail = $demand->getContent()['user_mail']; // Mail to
        // Send Mail
        $this->sendEmailMessage($rendered, $subject, $userMail);

        // Send admin mail
        $this->sendNewDemandAdminMail($demand, 'Admin - User registraiton request');
    }

    /**
     * New comment mailling
     *  To user or To Admin
     * @param Demand $demand
     * @param User $currentUser
     */
    public function newCommentSendMail(Demand $demand, User $currentUser) {

        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/NewComment:newComment.html.twig"; // Template
        $subject = $this->getSubject("New comment on your demand"); // Subject
        $demandUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_demand', array('id' => $demand->getId()), true);

        // Define target mail and template user to return
        if ($currentUser->getDn() == "uid=sys,dc=ldap,dc=test" || $currentUser->getDn() == "uid=admin,dc=ldap,dc=test") {
            // If user is admin, set Ldap User mail
            $mailTo = $demand->getApplicant()->getEmail();
            $templateUser = $demand->getApplicant();
        } else {
            // If user is LDAP user, Set admin mail
            $mailTo = $this->adminMail;
            $templateUser = null;
        }
        $rendered = $this->templating->render($template, array(
            'demand' => $demand,
            'user' => $templateUser,
            'demand_url' => $demandUrl
        )); // Template rendering

        // Send message
        $this->sendEmailMessage($rendered, $subject, $mailTo);
    }

    /**
     * Change status send mail
     *  To user
     * @param Demand $demand
     * @param User $currentUser
     */
    public function changeStatusSendMail(Demand $demand, User $currentUser) {
        // Send User mail
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/ChangeStatus:changeStatus.html.twig"; // Template
        $subject = $this->getSubject("Demand status change"); // Subject
        $demandUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_demand', array('id' => $demand->getId()), true);
        $rendered = $this->templating->render($template, array(
            'demand' => $demand,
            'user' => $demand->getApplicant(),
            'demand_url' => $demandUrl
        )); // Template rendering
        $userMail = $demand->getApplicant()->getEmail(); // mail to

        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $userMail);
    }

    /**
     * User Registration infos, send mail
     *  To User exclusively
     * @param Demand $demand
     * @param array $newUser
     */
    public function processNewUserSendMail(Demand $demand, array $newUser) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Registration:userRegistrationDone.html.twig"; // Template
        $subject = $this->getSubject('Registration done!'); // Subject
        $homepageUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_homepage', array(), true);
        $profileUrl = $this->router->generate('spirit_dev_dbox_user_bundle_profile_edit', array(), true);
        $rendered = $this->templating->render($template, array(
            'firstname' => $newUser['givenName'],
            'lastname' => $newUser['sn'],
            'username' => $newUser['uid'],
            'mail' => $newUser['mail'],
            'clear_password' => $newUser['clear_password'],
            'homepage_url' => $homepageUrl,
            'profile_url' => $profileUrl
        )); // Template rendering
        $userMail = $newUser['mail']; // mail to

        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $userMail);
    }

    /**
     * New demand project related
     *  To user
     *  To admin
     * @param Demand $demand
     * @param Project $project
     */
    public function projectRequestSendMail(Demand $demand, Project $project) {
        // User mail
        $template = "SpiritDevDBoxPortalBundle:Mailer/Project:projectRequest.html.twig"; // Template
        $subject = $this->getSubject('Project creation request - ' . $project->getName()); // Subject
        $demandUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_demand', array('id' => $demand->getId()), true);
        $rendered = $this->templating->render($template, array(
            'firstname' => $demand->getApplicant()->getFirstName(),
            'lastname' => $demand->getApplicant()->getLastName(),
            'project_name' => $project->getName(),
            'project_description' => $project->getDescription(),
            'project_owner' => $project->getOwner(),
            'project_team' => $project->getTeamMembers(),
            'demand_url' => $demandUrl
        )); // Template rendering
        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $demand->getApplicant()->getEmail());

        // Admin mail
        $this->sendNewDemandAdminMail($demand, 'Admin - Project creation request - ');
    }

    /**
     * New project auto processing
     *  To project team
     * @param Project $project
     */
    public function processProjectCreationSendMail(Project $project) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Project:projectCreation.html.twig"; // Template
        $subject = $this->getSubject('Project creation - ' . $project->getName()); // Subject
        $projectUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_project', array('pjt_name' => $project->getName()), true);
        $renderingDatas = array(
            'project' => $project,
            'project_url' => $projectUrl
        ); // Template rendering datas

        // Send mail(s) effectively
        foreach ($project->getTeamMembers() as $user) {
            $renderingDatas['firstname'] = $user->getFirstname();
            $renderingDatas['lastname'] = $user->getLastname();
            $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
            $this->sendEmailMessage($rendered, $subject, $user->getEmail());
        }
    }

    /**
     * New manager item mail process
     * @param $manager
     * @param Project $project
     * @return mixed
     */
    public function processProjectManagerItemCreation($manager, Project $project) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Project:projectNewManager.html.twig"; // Template
        $subject = $this->getSubject('Project creation - ' . $project->getName()); // Subject
        $projectUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_project', array('pjt_name' => $project->getName()), true);
        $renderingDatas = array(
            'manager' => $manager,
            'project' => $project,
            'project_url' => $projectUrl
        ); // Template rendering datas

        // Send mail(s) effectively
        foreach ($project->getTeamMembers() as $user) {
            $renderingDatas['firstname'] = $user->getFirstname();
            $renderingDatas['lastname'] = $user->getLastname();
            $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
            $this->sendEmailMessage($rendered, $subject, $user->getEmail());
        }
    }

    /**
     * Project add user auto processing
     *  To user himself
     *  To Admin
     *  To project owner
     * @param Project $project
     * @param User $newUser
     * @return mixed
     */
    public function projectAddUser(Project $project, User $newUser) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Project:projectAddUser.html.twig"; // Template
        $subject = $this->getSubject('Project user grant'); // Subject
        $projectUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_project', array('pjt_name' => $project->getName()), true);
        $renderingDatas = array(
            'project' => $project,
            'user' => $newUser,
            'project_url' => $projectUrl
        ); //Template rendering datas

        // Send mail to admin
        $renderingDatas['scope'] = 'admin';
        $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
        $this->sendEmailMessage($rendered, $subject, $this->adminMail);

        // Send mail to new user
        $renderingDatas['scope'] = 'user';
        $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
        $this->sendEmailMessage($rendered, $subject, $newUser->getEmail());

        // Send mail to owner
        $renderingDatas['scope'] = 'owner';
        $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
        $this->sendEmailMessage($rendered, $subject, $project->getOwner()->getEmail());

    }

    /**
     * Project remove user auto processing
     *  To user himself
     *  To Admin
     *  To project owner
     * @param Project $project
     * @param User $removedUser
     * @return mixed
     */
    public function projectRemoveUser(Project $project, User $removedUser) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Project:projectRemoveUser.html.twig"; // Template
        $subject = $this->getSubject('Project user removal'); // Subject
        $renderingDatas = array(
            'project' => $project,
            'user' => $removedUser
        ); //Template rendering datas

        // Send mail to admin
        $renderingDatas['scope'] = 'admin';
        $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
        $this->sendEmailMessage($rendered, $subject, $this->adminMail);

        // Send mail to new user
        $renderingDatas['scope'] = 'user';
        $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
        $this->sendEmailMessage($rendered, $subject, $removedUser->getEmail());

        // Send mail to owner
        $renderingDatas['scope'] = 'owner';
        $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
        $this->sendEmailMessage($rendered, $subject, $project->getOwner()->getEmail());
    }

    /**
     * Profile informations update
     *  To user only
     * @param User $user
     * @return mixed
     */
    public function profileInformationsUpdate(User $user) {
        // Define necessary vars
        $template = "UserBundle:Mailer/Profile:profileUpdateInfo.html.twig"; // Template
        $subject = $this->getSubject('Profile updated'); // Subject
        $appUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_homepage');
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'app_url' => $appUrl
        )); // Template rendering
        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $user->getEmail());
    }

    /**
     * Profile passord update
     *  To user only
     * @param User $user
     * @return mixed
     */
    public function profilePassUpdate(User $user) {
        // Define necessary vars
        $template = "UserBundle:Mailer/Profile:profileUpdatePass.html.twig"; // Template
        $subject = $this->getSubject('Profile password updated'); // Subject
        $appUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_homepage');
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'app_url' => $appUrl
        )); // Template rendering
        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $user->getEmail());
    }

    /**
     * Send mail for account de/activation, deletion
     * @param User $user
     * @param String $status deletion|deactivation|activation
     * @return mixed
     */
    public function accountUpdate(User $user, $status) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Profile:accountManagment.html.twig"; //template
        $subject = $this->getSubject('Account ' . $status); // Subject
        $appUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_homepage');
        $rendered = $this->templating->render($template, array(
            'status' => $status,
            'user' => $user,
            'app_url' => $appUrl,
            'admin_mail' => $this->adminMail
        )); // Template rendering
        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $user->getEmail());
    }

    /**
     * Send mail for a new Pipeline request
     *  To Admin
     *  To project owner
     * @param Demand $demand
     * @param $projectName
     * @return mixed
     */
    public function newPipelineRequestSendMail(Demand $demand, $projectName) {
        // User mail
        $template = "SpiritDevDBoxPortalBundle:Mailer/Pipeline:pipelineRequest.html.twig"; // Template
        $subject = $this->getSubject('New pipeline request - ' . $projectName); // Subject
        $demandUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_demand', array('id' => $demand->getId()), true);
        $rendered = $this->templating->render($template, array(
            'firstname' => $demand->getApplicant()->getFirstName(),
            'lastname' => $demand->getApplicant()->getLastName(),
            'demand' => $demand,
            'project_name' => $projectName,
            'demand_url' => $demandUrl
        )); // Template rendering
        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $demand->getApplicant()->getEmail());

        // Admin mail
        $this->sendNewDemandAdminMail($demand, 'Admin - New pipeline request - ' . $projectName);
    }

    /**
     * Send mail to project memebers to apply a project deletion
     *  To project team
     * @param Project $project
     * @return mixed
     */
    public function processProjectDeletionSendMail(Project $project) {
        // Define necessary vars
        $template = "SpiritDevDBoxPortalBundle:Mailer/Project:projectDeletion.html.twig"; // Template
        $subject = $this->getSubject('Project deletion - ' . $project->getName()); // Subject
        $renderingDatas = array(
            'project' => $project
        ); // Template rendering datas

        // Send mail(s) effectively
        foreach ($project->getTeamMembers() as $user) {
            $renderingDatas['firstname'] = $user->getFirstname();
            $renderingDatas['lastname'] = $user->getLastname();
            $rendered = $this->templating->render($template, $renderingDatas); // Template rendering
            $this->sendEmailMessage($rendered, $subject, $user->getEmail());
        }
    }

    /**
     * Send mail for a new Security request
     *  To Admin
     *  To project owner
     * @param Demand $demand
     * @param $projectName
     * @return mixed
     */
    public function newSecurityRequestSendMail(Demand $demand, $projectName) {
        // User mail
        $template = "SpiritDevDBoxPortalBundle:Mailer/Security:securityRequest.html.twig"; // Template
        $subject = $this->getSubject('New security assessment request - ' . $projectName); // Subject
        $demandUrl = $this->router->generate('spirit_dev_dbox_portal_bundle_demand', array('id' => $demand->getId()), true);
        $rendered = $this->templating->render($template, array(
            'firstname' => $demand->getApplicant()->getFirstName(),
            'lastname' => $demand->getApplicant()->getLastName(),
            'demand' => $demand,
            'project_name' => $projectName,
            'demand_url' => $demandUrl
        )); // Template rendering
        // Send mail effectively
        $this->sendEmailMessage($rendered, $subject, $demand->getApplicant()->getEmail());

        // Admin mail
        $this->sendNewDemandAdminMail($demand, 'Admin - New security request - ' . $projectName);
    }
    
}