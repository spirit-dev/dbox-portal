<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use SpiritDev\Bundle\DBoxPortalBundle\Entity\Feedback;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FeedbackController extends Controller {

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/feedbacks/register", name="spirit_dev_dbox_portal_bundle_register_feedback")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function registerFeedback(Request $request) {

        // Init values
        $resultStatus = JsonResponse::HTTP_CONFLICT;
        $resultText = 'error';

        // Register new feedback
        $feedbackContent = $request->request->get('content');
        // Check content
        if ($feedbackContent != null && $feedbackContent != "") {
            // Create feedback entity
            $feedback = new Feedback();
            $feedback->setContent($feedbackContent);
            $feedback->setSender($this->getCurrentUser());

            // save it in Database
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();

            // Update response content
            $resultStatus = JsonResponse::HTTP_OK;
            $resultText = 'ok';
        }

        return new JsonResponse($resultText, $resultStatus);
    }

    /**
     * Get Current User
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
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/feedbacks", name="spirit_dev_dbox_portal_bundle_feedbacks")
     * @Method("GET")
     * @Template()
     * @return array
     */
    public function countFeedbacksAction() {
        $em = $this->getDoctrine()->getManager();
        $countFeedbacks = $em->getRepository('SpiritDevDBoxPortalBundle:Feedback')->countUnreadedFeedbacks();

        return array('nb_feedbacks' => $countFeedbacks);
    }
}
