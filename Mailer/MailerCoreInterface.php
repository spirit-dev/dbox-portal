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
 * File           MailerCoreInterface.php
 * Updated the    25/05/16 10:44
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Mailer;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use SpiritDev\Bundle\DBoxUserBundle\Entity\User;

/**
 * Interface MailerInterface
 * @package SpiritDev\Bundle\DBoxPortalBundle\Mailer
 */
interface MailerCoreInterface {

    /**
     * New user registration mailling
     *  To User
     *  To Admin
     * @param Demand $demand
     */
    public function newUserSendMail(Demand $demand);

    /**
     * New comment mailling
     *  To user or To Admin
     * @param Demand $demand
     * @param User $currentUser
     */
    public function newCommentSendMail(Demand $demand, User $currentUser);

    /**
     * Change status send mail
     *  To user
     * @param Demand $demand
     * @param User $currentUser
     */
    public function changeStatusSendMail(Demand $demand, User $currentUser);

    /**
     * New demand project related
     *  To user
     *  To admin
     * @param Demand $demand
     * @param Project $project
     */
    public function projectRequestSendMail(Demand $demand, Project $project);

    /**
     * User Registration infos, send mail
     *  To User exclusively
     * @param Demand $demand
     * @param array $newUser
     */
    public function processNewUserSendMail(Demand $demand, array $newUser);

    /**
     * New project auto processing
     *  To project team
     * @param Project $project
     */
    public function processProjectCreationSendMail(Project $project);

    /**
     * New manager item mail process
     * @param $manager
     * @param Project $project
     * @return mixed
     */
    public function processProjectManagerItemCreation($manager, Project $project);

    /**
     * Project add user auto processing
     *  To user himself
     *  To Admin
     *  To project owner
     * @param Project $project
     * @param User $newUser
     * @return mixed
     */
    public function projectAddUser(Project $project, User $newUser);

    /**
     * Project remove user auto processing
     *  To user himself
     *  To Admin
     *  To project owner
     * @param Project $project
     * @param User $removedUser
     * @return mixed
     */
    public function projectRemoveUser(Project $project, User $removedUser);

    /**
     * Profile informations update
     *  To user only
     * @param User $user
     * @return mixed
     */
    public function profileInformationsUpdate(User $user);

    /**
     * Profile passord update
     *  To user only
     * @param User $user
     * @return mixed
     */
    public function profilePassUpdate(User $user);

    /**
     * Send mail for account de/activation, deletion
     * @param User $user
     * @param $status
     * @return mixed
     */
    public function accountUpdate(User $user, $status);

    /**
     * Send mail for a new Pipeline request
     *  To Admin
     *  To project owner
     * @param Demand $demand
     * @param $projectName
     * @return mixed
     */
    public function newPipelineRequestSendMail(Demand $demand, $projectName);

    /**
     * Send mail to project memebers to apply a project deletion
     *  To project team
     * @param Project $project
     * @return mixed
     */
    public function processProjectDeletionSendMail(Project $project);

}