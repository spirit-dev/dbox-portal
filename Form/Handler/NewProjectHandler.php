<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Form\Handler;

use Doctrine\ORM\EntityManager;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Demand;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class NewProjectHandler
 * @package SpiritDev\Bundle\DBoxPortalBundle\Form\Handler
 */
class NewProjectHandler {

    protected $request;
    protected $em;
    protected $form;
    protected $container;

    /**
     * Constructor
     * @param RequestStack $request
     * @param EntityManager $em
     * @param ContainerInterface $container
     */
    public function __construct(RequestStack $request, EntityManager $em, ContainerInterface $container) {
        // Setting datas
        $this->request = $request->getCurrentRequest();
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * Processing new user registration
     * @param Project $project
     * @return bool
     */
    public function process(Project $project) {
        $issue = null;
        // If request is POST
        if ('POST' === $this->request->getMethod()) {
            // Register demand
            $issue = $this->registerDemand($project);
        }
        return $issue;
    }

    /**
     * Process demand registration
     * @param Project $project
     * @return Demand
     */
    private function registerDemand(Project $project) {

        // Persist Project
        $this->em->persist($project);
        $this->em->flush();

        // Managing EM entities
        $status = $this->em->getRepository('SpiritDevBundleDBoxPortalBundle:Status')->findOneBy(array('canonicalName' => 'new'));
        $type = $this->em->getRepository('SpiritDevBundleDBoxPortalBundle:Type')->findOneBy(array('canonicalName' => 'new_project'));

        // Creating empty demand
        $demand = new Demand();
        // Setting datas
        $demand->setAskdate(new \DateTime());
        $demand->setApplicant($this->getCurrentUser());
        $demand->setStatus($status);
        $demand->setType($type);
        $demand->setContent($this->setContentData($project));

        // Persisting EM entity
        $this->em->persist($demand);
        $this->em->flush();

        return $demand;
    }

    /**
     * Getting current session user
     * @return mixed
     */
    private function getCurrentUser() {
        return $this->container->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * Setting demand content
     * @param Project $project
     * @return array
     */
    private function setContentData(Project $project) {

        $owner = $project->getOwner()->getCommonName();

        $teamMembersArray = "";
        $teamMembers = $project->getTeamMembers();
        $tml = count($teamMembers);
        $tmc = 1;
        foreach ($teamMembers as $member) {
            $teamMembersArray .= $member->getCommonName();
            if ($tmc < $tml) $teamMembersArray .= ", ";
            $tmc++;
        }

        return [
            "name" => $project->getName(),
            "description" => $project->getDescription(),
            "owner" => $owner,
            "team_members" => $teamMembersArray,
            "git_issues" => $project->isGitLabIssueEnabled(),
            "git_wiki" => $project->isGitLabWikiEnabled(),
            "git_snippets" => $project->isGitLabSnippetsEnabled(),
            "id" => $project->getId()
        ];
    }

}