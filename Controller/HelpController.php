<?php
/**
 * Created by PhpStorm.
 * User: JuanitoP
 * Date: 15/04/2016
 * Time: 16:42
 */

namespace SpiritDev\Bundle\DBoxPortalBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class HelpController
 * @package SpiritDev\Bundle\DBoxPortalBundle\Controller
 * @Route("/help")
 */
class HelpController extends Controller {

    /**
     * @Route("/", name="spirit_dev_dbox_portal_bundle_help")
     * @Template()
     */
    public function indexAction() {
        return array();
    }

}