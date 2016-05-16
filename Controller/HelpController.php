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
 * File           HelpController.php
 * Updated the    15/05/16 11:47
 */

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