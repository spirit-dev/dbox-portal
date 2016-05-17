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
 * File           ProjectController.php
 * Updated the    17/05/16 21:17
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProjectController
 * @package SpiritDev\Bundle\DBoxPortalBundle\Controller
 */
class ProjectController extends Controller {

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project/{pjt_name}", name="spirit_dev_dbox_portal_bundle_project")
     * @Template()
     * @param $pjt_name
     * @return array
     */
    public function projectAction($pjt_name) {
        // Get project entity
        $em = $this->getDoctrine()->getEntityManager();
        $project = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array('name' => $pjt_name));
        // Get available users
        $availableUsers = $this->getAvailableUsers($em, $project);

        // Get VCS datas
        if ($project->isVcsManaged()) {
            $gitLabAPI = $this->get('spirit_dev_dbox_portal_bundle.api.gitlab');
            $gitDatas = $gitLabAPI->getProject($project->getGitLabProjectId());
            $gitBranches = $gitLabAPI->defineGitCommits($project);
            $gitTags = $gitLabAPI->getProjectTags($project->getGitLabProjectId());
        } else {
            $gitDatas = null;
            $gitBranches = null;
            $gitTags = null;
        }

        // Get PM datas
        if ($project->isPmManaged()) {
            $pmApi = $this->get('spirit_dev_dbox_portal_bundle.api.redmine');
            $pmBugs = $pmApi->getIssues($project, $this->getParameter('spirit_dev_d_box_portal.redmine_api.bug_tracker'));
            $pmEvols = $pmApi->getIssues($project, $this->getParameter('spirit_dev_d_box_portal.redmine_api.evol_tracker'));
            $pmTests = $pmApi->getIssues($project, $this->getParameter('spirit_dev_d_box_portal.redmine_api.test_tracker'));
            $pmQa = $pmApi->getIssues($project, $this->getParameter('spirit_dev_d_box_portal.redmine_api.qa_tracker'));
        } else {
            $pmBugs = null;
            $pmEvols = null;
            $pmTests = null;
            $pmQa = null;
        }

        // Get CI infos
        if ($project->isCiDevManaged()) {
            $jenkinsAPI = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins');
            // Get remotes ACTIVE managed pipelines
            $ciActivePipelines = $em->getRepository('SpiritDevDBoxPortalBundle:ContinuousIntegration')->findBy(array(
                'project' => $project,
                'active' => true
            ));
            $ciViewUrl = $this->getParameter("spirit_dev_d_box_portal.jenkins_api.protocol") . $this->getParameter("spirit_dev_d_box_portal.jenkins_api.url") . "/view/";
            $buildsToReturn = null;
            foreach ($ciActivePipelines as $pipe) {
                // Redefine entity params to apply json_decode function
                $params = array();
                foreach ($pipe->getParameters() as $param) {
                    $params[] = json_decode($param);
                }
                $pipe->setParameters($params);
                // Get builds info
                $builds = $jenkinsAPI->getBuilds($pipe);
                if (count($builds) > 0) {
                    $buildsToReturn[] = array(
                        'ci' => $pipe->getCiName(),
                        'result' => $builds[0]->getResult(),
                        'timestamp' => substr($builds[0]->getTimestamp(), 0, strpos($builds[0]->getTimestamp(), '.')),
                        'url' => $builds[0]->getUrl(),
                        'number' => $builds[0]->getNumber()
                    );
                }
            }
            // Get Remote UNACTIVE pipelines
            $ciUnactivePipelines = $em->getRepository('SpiritDevDBoxPortalBundle:ContinuousIntegration')->findBy(array(
                'project' => $project,
                'active' => false
            ));
        } else {
            $ciActivePipelines = null;
            $ciUnactivePipelines = null;
            $buildsToReturn = null;
        }

        // Get QA infos
        if ($project->isQaDevManaged()) {
            $qaApi = $this->get('spirit_dev_dbox_portal_bundle.api.sonar');
            $qaIssues = [
                'qaResolvedIssues' => $qaApi->getIssuesPerResolution($project, "true"),
                'qaUnresolvedIssues' => $qaApi->getIssuesPerResolution($project, "false"),
                'qaInfoIssues' => $qaApi->getIssuesPerSeverity($project, "INFO"),
                'qaMinorIssues' => $qaApi->getIssuesPerSeverity($project, "MINOR"),
                'qaMajorIssues' => $qaApi->getIssuesPerSeverity($project, "MAJOR"),
                'qaCriticalIssues' => $qaApi->getIssuesPerSeverity($project, "CRITICAL"),
                'qaBlockerIssues' => $qaApi->getIssuesPerSeverity($project, "BLOCKER")
            ];
        } else {
            $qaIssues = null;
        }

        return array(
            'project' => $project,
            'git_datas' => $gitDatas,
            'git_branches' => $gitBranches,
            'git_tags' => $gitTags,
            'available_user' => $availableUsers,
            'pm_bugs' => ($pmBugs != null && array_key_exists('total_count', $pmBugs)) ? $pmBugs['total_count'] : null,
            'pm_evols' => ($pmEvols != null && array_key_exists('total_count', $pmEvols)) ? $pmEvols['total_count'] : null,
            'pm_tests' => ($pmTests != null && array_key_exists('total_count', $pmTests)) ? $pmTests['total_count'] : null,
            'pm_qa' => ($pmQa != null && array_key_exists('total_count', $pmQa)) ? $pmQa['total_count'] : null,
            'managed_pipes' => $ciActivePipelines,
            'builds' => $buildsToReturn,
            'unmanaged_pipes' => $ciUnactivePipelines,
            'ci_view_url' => isset($ciViewUrl) ? $ciViewUrl : null,
            'qa_issues' => $qaIssues
        );
    }

    /**
     * Extract available user to add to project
     * @param EntityManager $em
     * @param Project $project
     * @return array
     */
    private function getAvailableUsers(EntityManager $em, Project $project) {
        $users = $em->getRepository('SpiritDevDBoxUserBundle:User')->getUsableUsers()->getQuery()->getResult();

        $returnedUsers = array();

        foreach ($users as $user) {
            $userIsMember = false;
            foreach ($project->getTeamMembers() as $member) {
                if ($user == $member) {
                    $userIsMember = true;
                }
            }
            if (!$userIsMember) {
                $returnedUsers[] = $user;
            }
        }
        return $returnedUsers;
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project_rm_user", name="spirit_dev_dbox_portal_bundle_project_rm_user")
     * @param Request $request
     * @return RedirectResponse
     */
    public function projectRemoveUserAction(Request $request) {

        // Get entity manager
        $em = $this->getDoctrine()->getEntityManager();

        // Get user to remove entity
        $userToRemove = $em->getRepository('SpiritDevDBoxUserBundle:User')->findOneBy(array(
            'id' => $request->request->get('userId')
        ));
        // Get project entity
        $project = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array(
            'id' => $request->request->get('projectId')
        ));

        // Get administrative role
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        // If user is project owner and if user to remove is different of owner
        if (($project->getOwner() == $this->getCurrentUser() || $isAdmin) && $userToRemove != $project->getOwner()) {
            // Update Gitlab
            $this->get('spirit_dev_dbox_portal_bundle.api.gitlab')->delTeamMember($project->getGitLabProjectId(), $userToRemove);
            // Update PM
            $this->get('spirit_dev_dbox_portal_bundle.api.redmine')->removeProjectMemberships($project, $userToRemove);
            // Update Jenkins
            $this->get('spirit_dev_dbox_portal_bundle.todos.manager')->addTodo(sprintf('Jenkins: Remove user %s from project %s', $userToRemove->getUsername(), $project->getName()));
            // Update Sonar
            $this->get('spirit_dev_dbox_portal_bundle.api.sonar')->removePermission($userToRemove, $project);

            // Update entity
            $project->removeTeamMember($userToRemove);
            $em->flush();
            // Send mail to removed user, project owner, admin
            $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
            $mailer->projectRemoveUser($project, $userToRemove);

            $this->get('session')->getFlashBag()->set('success', 'flashbag.project.rm_member.success');
        } else {
            $this->get('session')->getFlashBag()->set('error', 'flashbag.project.rm_member.not_allowed');
        }

        return new RedirectResponse($this->getRedirectionUrl($project->getName()));
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
     * Generate the redirection url when editing is completed.
     * @param $projectName
     * @return string
     */
    protected function getRedirectionUrl($projectName) {
        return $this->container->get('router')->generate('spirit_dev_dbox_portal_bundle_project', array('pjt_name' => $projectName));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project_add_user", name="spirit_dev_dbox_portal_bundle_project_add_user")
     * @param Request $request
     * @return RedirectResponse
     */
    public function projectAddUserAction(Request $request) {

        // Get entity manager
        $em = $this->getDoctrine()->getEntityManager();

        // Get user to remove entity
        $userToAdd = $em->getRepository('SpiritDevDBoxUserBundle:User')->findOneBy(array(
            'id' => $request->request->get('userId')
        ));
        // Get project entity
        $project = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array(
            'id' => $request->request->get('projectId')
        ));

        // Get administrative role
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        // Check if user is project owner
        if (($project->getOwner() == $this->getCurrentUser() || $isAdmin)) {
//            // Update Gitlab
            $this->get('spirit_dev_dbox_portal_bundle.api.gitlab')->addTeamMember($project->getGitLabProjectId(), $userToAdd);
            // Update PM
            $redmineApi = $this->get('spirit_dev_dbox_portal_bundle.api.redmine');
            $redmineApi->addProjectMemberships($project, $userToAdd, $this->getParameter('spirit_dev_d_box_portal.redmine_api.role_dev'));
            // Update Jenkins
            $this->get('spirit_dev_dbox_portal_bundle.todos.manager')->addTodo(sprintf('Jenkins: Add user %s to project %s', $userToAdd->getUsername(), $project->getName()));
            // Update Sonar
            $this->get('spirit_dev_dbox_portal_bundle.api.sonar')->addPermission($userToAdd, $project);

            // Update entity
            $project->addTeamMember($userToAdd);
            $em->flush();
            // Send mail to added user, project owner, admin
            $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
            $mailer->projectAddUser($project, $userToAdd);

            $this->get('session')->getFlashBag()->set('success', 'flashbag.project.add_member.success');
        } else {
            $this->get('session')->getFlashBag()->set('error', 'flashbag.project.add_member.not_allowed');
        }

        return new RedirectResponse($this->getRedirectionUrl($project->getName()));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project_launch_ci_job", name="spirit_dev_dbox_portal_bundle_project_launch_ci_job")
     * @param Request $request
     * @return JsonResponse
     */
    public function projectLaunchCiJobAction(Request $request) {

        // Get entity manager
        $em = $this->getDoctrine()->getEntityManager();

        // Get user to remove entity
        $ciToLaunch = $em->getRepository('SpiritDevDBoxPortalBundle:ContinuousIntegration')->findOneBy(array(
            'id' => $request->request->get('ciId')
        ));

        $jkApi = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins');
        $jkJobLaunch = $jkApi->launchJob($ciToLaunch);

        $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        if ($jkJobLaunch) {
            $status = JsonResponse::HTTP_OK;
        }
        return new JsonResponse($jkJobLaunch, $status);
    }

    /**
     * Security("has_role('ROLE_USER')")
     * @Route("/project_ci_job_progress", name="spirit_dev_dbox_portal_bundle_project_ci_job_progress")
     * @param Request $request
     * @return JsonResponse
     */
    public function projectLaunchedProgressionAction(Request $request) {

        // Get entity manager
        $em = $this->getDoctrine()->getEntityManager();

        // Get user to remove entity
        $ciLaunched = $em->getRepository('SpiritDevDBoxPortalBundle:ContinuousIntegration')->findOneBy(array(
            'id' => $request->request->get('ciId')
        ));

        $ciProgress = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins')->getProgression($ciLaunched);
        return new JsonResponse(json_encode($ciProgress), JsonResponse::HTTP_OK);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project_ci_job_progress_sse", name="spirit_dev_dbox_portal_bundle_project_ci_job_progress_sse")
     * @param Request $request
     */
    public function projectLaunchedProgressionSSEAction(Request $request) {

        // Get CI id progression to reach
        $ciId = $request->get('ciId');

        // Get entity manager
        $em = $this->getDoctrine()->getEntityManager();

        // Get user to remove entity
        $ciLaunched = $em->getRepository('SpiritDevDBoxPortalBundle:ContinuousIntegration')->findOneBy(array(
            'id' => $ciId
        ));

        function sendMsg($id, $msg) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache'); // recommended to prevent caching of event data.
            echo "event: progress\n";
            echo 'data: {"progress": "' . $msg . '"}';
            echo "\n\n";
            ob_flush();
            flush();
        }

        $serverTime = time();
        while (1) {
            $ciProgress = $this->get('spirit_dev_dbox_portal_bundle.api.jenkins')->getProgression($ciLaunched);
            sendMsg($serverTime, $ciProgress);
        }

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project_add_manager", name="postal_bundle_project_add_manager")
     * @param Request $request
     * @return RedirectResponse
     */
    public function projectAddItem(Request $request) {
        // Get request datas
        $item = $request->request->get('manager');
        $pjtId = $request->request->get('pjt_id');

        // Get Project entity
        $em = $this->getDoctrine()->getManager();
        $projectToManage = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array('id' => $pjtId));

        // Initialize values
        $xhrStatus = JsonResponse::HTTP_CONFLICT;
        $result = "No referenced manager";

        // Run depending on item manager focused
        if (($item == "vcs" || $item == "pm" || $item == "ci" || $item == "qa") && $projectToManage) {

            // Special preparation in case if VCS
            if ($item == "vcs") {
                $projectToManage->setGitLabSnippetsEnabled(true);
                $projectToManage->setGitLabWikiEnabled(true);
                $projectToManage->setGitLabIssueEnabled(true);
            }

            $result = $this->get('spirit_dev_dbox_portal_bundle.admin.processor')->processProjectManager($projectToManage, $item);

            // Analyze result
            if ($item == "vcs") {
                // Check if an error occured
                $hasError = $this->checkHasError($result, 'VCS ERROR');

                if ($hasError['has_error']) {
                    // Rollback if VCS error occured
                    $projectToManage->setVcsManaged(false);
                    $projectToManage->setGitLabSnippetsEnabled(false);
                    $projectToManage->setGitLabWikiEnabled(false);
                    $projectToManage->setGitLabIssueEnabled(false);
                    $result = $hasError['error_text'];
                } else {
                    $projectToManage->setVcsManaged(true);
                    $xhrStatus = JsonResponse::HTTP_OK;
                    $result = 'OK';
                }

            } else if ($item == "pm") {
                // Check if an error occured
                $hasError = $this->checkHasError($result, 'PM ERROR');

                if ($hasError['has_error']) {
                    $projectToManage->setPmManaged(false);
                    $result = $hasError['error_text'];
                } else {
                    $projectToManage->setPmManaged(true);
                    $xhrStatus = JsonResponse::HTTP_OK;
                    $result = 'OK';
                }

            } else if ($item == "ci") {
                // Check if a ci or qa error occured
                $hasError = $this->checkHasError($result, 'CI ERROR');

                if ($hasError['has_error']) {
                    $projectToManage->setCiDevManaged(false);
                    $result = $hasError['error_text'];
                } else {
                    $projectToManage->setCiDevManaged(true);
                    $xhrStatus = JsonResponse::HTTP_OK;
                    $result = 'OK';
                }
            } else if ($item == "qa") {
                $hasError = $this->checkHasError($result, 'QA ERROR');
                if ($hasError['has_error']) {
                    $projectToManage->setQaDevManaged(false);
                    $result = $hasError['error_text'];
                } else {
                    $projectToManage->setQaDevManaged(true);
                    $xhrStatus = JsonResponse::HTTP_OK;
                    $result = 'OK';
                }
            }
            // Fix DB changes
            $em->flush();
            // send mail
            $mailer = $this->get('spirit_dev_dbox_portal_bundle.mailer');
            $mailer->processProjectManagerItemCreation($item, $projectToManage);
        }

        return new JsonResponse($result, $xhrStatus);
    }

    /**
     * Function checking if API returned values has error
     * @param $result
     * @param $slotErrorName
     * @return array
     */
    private function checkHasError($result, $slotErrorName) {
        // initialize values
        $hasError = false;
        $errorText = null;

        // Loop on each returned element
        for ($i = 0; $i < count($result['data']); $i++) {
            if ($result['data'][$i]['val_name'] == $slotErrorName) {
                $hasError = true;
                $errorText = $result['data'][$i]['data'];
                break;
            }
        }

        // Return values
        return [
            'has_error' => $hasError,
            'error_text' => $errorText
        ];
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/project/security_report/{report}/{pjt_name}", name="spirit_dev_dbox_portal_bundle_project_security_report")
     * @param $report
     * @param $pjt_name
     * @return array
     */
    public function displaySecurityReport($report, $pjt_name) {
        // Get Entity Manager
        $em = $this->getDoctrine()->getManager();
        // Get related project
        $project = $em->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array('name' => $pjt_name));
        // Get Dev Pipeline
        $devPipeline = $em->getRepository('SpiritDevDBoxPortalBundle:ContinuousIntegration')->findOneBy(array(
            'project' => $project,
            'active' => true,
            'forDevelopment' => true
        ));

        // Initialize variables
        $fullPath = null;
        $htmlContent = "<div style='text-align: center;'>
            <h1 style='color: #b20000;'>Sorry, nothing to display yet!</h1>
            <hr style='margin: 0 50px 0 50px;'>
            <h3>It's possible that no reports are available yet.</h3>
            <h3>You may retry later.</h3>
            <h4>We apologize for it :(</h4>
            </div>";

        if ($devPipeline) {
            // Get File System
            $fs = new Filesystem();

            // Initialize path with Dev Pipeline
            $basePath = "/var/lib/jenkins/workspace/" . $devPipeline->getCiName();

            // Generate full report path depending on repot needed
            if ($report == 1) {
                $fullPath = $basePath . "/" . "dependency-check-report.html";
            } else if ($report == 2) {
                $fullPath = $basePath . "/" . "dependency-check-vulnerability.html";
            } else if ($report == 3) {
                $fullPath = $basePath . "/" . "zapScannerReport.html";
            }

            // Read file if exists
            if ($fullPath != null && $fs->exists($fullPath)) {
                $htmlContent = file_get_contents($fullPath);
            }
        }

        // Return response
        return new Response($htmlContent);
    }
}
