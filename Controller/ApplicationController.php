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
 * File           ApplicationController.php
 * Updated the    15/05/16 11:47
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;

/**
 * Class ApplicationController
 * @package SpiritDev\Bundle\DBoxPortalBundle\Controller
 */
class ApplicationController extends Controller {

    /**
     * @return array
     *
     * @Route("/lmapplications", name="spirit_dev_dbox_portal_bundle_applications")
     * @Template()
     */
    public function applicationsAction() {

        $em = $this->getDoctrine()->getManager();

        $applications = $em->getRepository('SpiritDevDBoxPortalBundle:Application')->findAll();

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

        $appli = $em->getRepository('SpiritDevDBoxPortalBundle:Application')->findOneBy(array('id' => $app));

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

        $appli = $em->getRepository('SpiritDevDBoxPortalBundle:Application')->findOneBy(array('canonicalName' => $app));

        return array('appli' => $appli);
    }

}