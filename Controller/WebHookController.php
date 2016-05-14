<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class WebHookController extends Controller {

    /**
     * @Route("/web_hook/update/nb_commits/{pjt_id}", name="spirit_dev_dbox_portal_bundle_webhook_pjt_update_nbcommits")
     * @param $pjt_id
     * @return Response
     */
    public function webHookUpdateNbCommits($pjt_id) {
        // Getting gitlab API
        $gitLabAPI = $this->get('spirit_dev_dbox_portal_bundle.api.gitlab');
        // Getting project
        $project = $this->getDoctrine()->getRepository('SpiritDevDBoxPortalBundle:Project')->findOneBy(array('id' => $pjt_id));

        // Update project datas with nbCommits
        $gitBranches = $gitLabAPI->defineGitCommits($project);
        if (count($gitBranches) > 0) {
            $this->registerNbCommits($project, $gitBranches);
        }

        // Returning response
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * Register NB commits
     * @param Project $project
     * @param array $gitBranches
     * @return Project
     */
    private function registerNbCommits(Project $project, array $gitBranches) {
        // Initialize datas
        $totalNbCommits = 0;
        // Loop on each branches
        for ($i = 0; $i < count($gitBranches); $i++) {
            // Increment total commits
            $totalNbCommits += $gitBranches[$i]["nb_commits"];
        }
        // Update project
        if ($project->getGitNbCommits() != $totalNbCommits) {
            // Setting nb commits
            $project->setGitNbCommits($totalNbCommits);
            // Setting last commit date
            $project->setGitCommitLastUpdate(new \DateTime());
            $this->getDoctrine()->getEntityManager()->flush();
        }
        // Return
        return $project;
    }
}