<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use SpiritDev\Bundle\DBoxPortalBundle\Entity\Communication;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class CommunicationController extends Controller {

    /**
     * @Route("/communication/available", name="spirit_dev_dbox_portal_bundle_communications_available")
     * @Security("has_role('ROLE_USER')")
     */
    public function getAvailableComsAction() {

        // Getting necessary informations
        $currentUser = $this->getCurrentUser();
        $returnedComs = array();
        $i = 0;

        // Get communications dependings on roles
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            // Check DB
            $coms = $this->getDoctrine()->getRepository('SpiritDevBundleDBoxPortalBundle:Communication')->findAvailableCommunications($currentUser->getViewedCommunications());
            // Recompose returned datas
            foreach ($coms as $com) {
                $returnedComs[$i]['id'] = $com->getId();
                $returnedComs[$i]['title'] = $com->getTitle();
                $returnedComs[$i]['content'] = $com->getContent();
                $returnedComs[$i]['type'] = $com->getType();
                $i++;
            }
        }

        // Returned JSON Datas (empty or full)
        $response = new JsonResponse();
        $response->setData(array(
            'communications' => $returnedComs
        ));
        return $response;
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
     * @Route("/communication/setviewed", name="spirit_dev_dbox_portal_bundle_communications_set_viewed")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function setViewedComAction(Request $request) {
        // Getting necessary informaitons
        $em = $this->getDoctrine()->getEntityManager();
        $currentUser = $this->getCurrentUser();
        $communication = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Communication')->findOneBy(array(
            'id' => $request->request->get('com_id')
        ));

        // Updating entity
        $currentUser->addViewedCommunication($communication);
        $em->flush();

        // Return JSON Response
        $response = new JsonResponse();
        $response->setData(array(
            'user_viewed_coms' => $currentUser->getViewedCommunications()
        ));
        return $response;
    }

    /**
     * @Route("/communication/setunviewed", name="spirit_dev_dbox_portal_bundle_communications_set_unviewed")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function setUnviewedComAction(Request $request) {
        // Getting necessary informaitons
        $em = $this->getDoctrine()->getEntityManager();
        $currentUser = $this->getCurrentUser();
        $communication = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Communication')->findOneBy(array(
            'id' => $request->request->get('com_id')
        ));

        // Updating entity
        $currentUser->removeViewedCommunication($communication);
        $em->flush();

        // Return JSON Response
        $response = new JsonResponse();
        $response->setData(array(
            'user_viewed_coms' => $currentUser->getViewedCommunications()
        ));
        return $response;
    }

}