<?php

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

class ApplicationController extends Controller {

    /**
     * @return array
     *
     * @Route("/lmapplications", name="spirit_dev_dbox_portal_bundle_applications")
     * @Template()
     */
    public function applicationsAction() {

        $em = $this->getDoctrine()->getManager();

        $applications = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Application')->findAll();

        return array('applications' => $applications);
    }

    /**
     * This route is called with applicaiton ID, and redirect only on application found
     * @param $app
     * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @Security("has_role('ROLE_USER')")
     * @Route("/appli/{app}", name="spirit_dev_dbox_portal_bundle_application_redirect")
     */
    public function appliRedirectAction($app) {

        $em = $this->getDoctrine()->getManager();

        $appli = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Application')->findOneBy(array('id' => $app));

        if (!$appli) {
            throw $this->createNotFoundException('The application does not exist');
        }

        return $this->redirectToRoute('spirit_dev_dbox_portal_bundle_application', array('app' => $appli->getCanonicalName()), 301);
    }

    /**
     * This route is called from rediction of the previous route.
     * It's called only on application existence
     * @param $app
     * @return array
     *
     * @Security("has_role('ROLE_USER')")
     * @Route("/app/{app}", name="spirit_dev_dbox_portal_bundle_application")
     * @Template()
     */
    public function appliAction($app) {

        $em = $this->getDoctrine()->getManager();

        $appli = $em->getRepository('SpiritDevBundleDBoxPortalBundle:Application')->findOneBy(array('canonicalName' => $app));

        return array('appli' => $appli);
    }

}